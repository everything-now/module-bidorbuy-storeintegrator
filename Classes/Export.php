<?php

namespace Bidorbuy\StoreIntegrator\Classes;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Bidorbuy\StoreIntegrator\Helper\DataHelper;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Export
{
    const XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';

    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    protected $dataHelper;
    protected $shippingMethodsTitles;
    protected $productCollection;
    protected $moduleList;
    protected $categoryRepository;
    protected $stockItemModel;
    protected $storeModel;
    protected $currencyConverter;
    protected $catalogOutputHelper;
    protected $productRepository;
    protected $productMediaConfig;
    protected $shippingConfigModel;
    protected $groupedProductModel;
    protected $configurableProductModel;
    protected $productModel;
    protected $attributesCollection;
    protected $weightUnit;
    // phpcs:enable

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\CatalogInventory\Model\Stock\Item $stockItemModel,
        \Magento\Store\Model\Store $storeModel,
        \Magento\Directory\Helper\Data $currencyConverter,
        \Magento\Catalog\Helper\Output $catalogOutputHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\Product\Media\Config $productMediaConfig,
        \Magento\Shipping\Model\Config $shippingConfigModel,
        \Magento\GroupedProduct\Model\Product\Type\Grouped $groupedProductModel,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableProductModel,
        \Magento\Catalog\Model\Product $productModel,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $attributesCollection,
        BidorbuyHelper $bidorbuyHelper,
        DataHelper $dataHelper
    ) {
        $this->bidorbuyHelper = $bidorbuyHelper;
        $this->dataHelper = $dataHelper;
        $this->productCollection = $productCollection;
        $this->moduleList = $moduleList;
        $this->categoryRepository = $categoryRepository;
        $this->stockItemModel = $stockItemModel;
        $this->storeModel = $storeModel;
        $this->currencyConverter = $currencyConverter;
        $this->catalogOutputHelper = $catalogOutputHelper;
        $this->productRepository = $productRepository;
        $this->productMediaConfig = $productMediaConfig;
        $this->shippingConfigModel = $shippingConfigModel;
        $this->groupedProductModel = $groupedProductModel;
        $this->configurableProductModel = $configurableProductModel;
        $this->productModel = $productModel;
        $this->attributesCollection = $attributesCollection;
        $this->shippingMethodsTitles = $this->getShippingMethodsTitles();
        $this->weightUnit = $this->getWeightUnit();
    }

    public function export($token, $productsIds = 0)
    {
        $exportConfiguration = [
            bobsi\Settings::PARAM_IDS => $productsIds,
            bobsi\Tradefeed::NAME_EXCLUDED_ATTRIBUTES =>
                [
                    bobsi\Tradefeed::NAME_PRODUCT_ATTR_WIDTH,
                    bobsi\Tradefeed::NAME_PRODUCT_ATTR_HEIGHT,
                    bobsi\Tradefeed::NAME_PRODUCT_ATTR_LENGTH,
                ],
            bobsi\Settings::PARAM_CALLBACK_GET_PRODUCTS => [$this, 'getAllProducts'],
            bobsi\Settings::PARAM_CALLBACK_GET_BREADCRUMB => [$this, 'getBreadcrumb'],
            bobsi\Settings::PARAM_CALLBACK_EXPORT_PRODUCTS => [$this, 'exportProducts'],
            bobsi\Settings::PARAM_CATEGORIES => $this->dataHelper->getExportCategoriesIds(
                $this->bidorbuyHelper->getCore()->getSettings()->getExcludeCategories()
            ),
            bobsi\Settings::PARAM_ALLOW_OFFERS_CATEGORIES => $this->dataHelper->getAllowOffersCategoriesIds(
                $this->bidorbuyHelper->getCore()->getSettings()->getIncludeAllowOffersCategories()
            ),
            bobsi\Settings::PARAM_EXTENSIONS => []
        ];

        $attributes = $this->attributesCollection->getItems() ?: [];

        foreach ($attributes as $attribute) {
            if (!$this->isAttributeUsedInProductTitle($attribute)) {
                $exportConfiguration[bobsi\Tradefeed::NAME_EXCLUDED_ATTRIBUTES][] = $attribute->getFrontendLabel();
            }
        }

        $modules = $this->moduleList->getAll();

        $extensions = [];
        foreach ($modules as $module) {
            $extensions[$module['name']] = "{$module['name']} {$module['setup_version']}";
        }

        $exportConfiguration[bobsi\Settings::PARAM_EXTENSIONS] = $extensions;

        $this->bidorbuyHelper->getCore()->export($token, $exportConfiguration);
    }

    public function getBreadcrumb($categoryId)
    {
        if (!$categoryId) {
            return '';
        }

        $rootCategoryId = $this->storeModel->getRootCategoryId();
        // Use category repository instead category model for prevent cache issues, see BOBSI-21
        $category = $this->categoryRepository->get($categoryId);

        $parentsIds = $category->getParentIds();
        $categories = [];

        $parents = $category->getCollection()
            ->addIdFilter($parentsIds)
            ->addAttributeToSelect('name')
            ->setOrder('level', Collection::SORT_ORDER_ASC);

        foreach ($parents as $parentCat) {
            if ($parentCat->getId() != $rootCategoryId && $parentCat->getId() != 1) {
                array_push($categories, $parentCat->getName());
            }
        }

        array_push($categories, $category->getName());

        return implode(' > ', $categories);
    }

    public function getAllProducts()
    {
        return array_keys(
            $this->productCollection->addAttributeToSelect('status')->addFieldToFilter(
                'status',
                Status::STATUS_ENABLED
            )->getItems()
        );
    }

    /**
     * Export Products
     *
     * @param array $productsIds ids
     * @param array $exportConfiguration configuration
     *
     * @return array
     */
    public function exportProducts(array $productsIds, $exportConfiguration = [])
    {
        $exportQuantityMoreThan = $this->bidorbuyHelper->getCore()->getSettings()->getExportQuantityMoreThan();
        $defaultStockQuantity = $this->bidorbuyHelper->getCore()->getSettings()->getDefaultStockQuantity();
        $exportProducts = [];

        foreach ($productsIds as $productId) {
            $product = $this->productRepository->getById($productId);

            $productCategories = $product->getCategoryIds();
            $productCategories = empty($productCategories) ? ['0'] : $productCategories;

            $categoriesMatching = array_intersect(
                $exportConfiguration[bobsi\Settings::PARAM_CATEGORIES],
                $productCategories
            );
            $allowOffersCategoriesMatching = array_intersect(
                $exportConfiguration[bobsi\Settings::PARAM_ALLOW_OFFERS_CATEGORIES],
                $productCategories
            );
            if (empty($categoriesMatching)) {
                continue;
            }

            $this->bidorbuyHelper->getCore()->logInfo('Processing product id: ' . $product->getId());

            if ($product->getStatus() != Status::STATUS_ENABLED) {
                $this->bidorbuyHelper->getCore()->logInfo(
                    'Product status: disabled. Product id: ' . $product->getId()
                );
                continue;
            }

            $productQuantity = $this->calcProductQuantity($product, $defaultStockQuantity);
            $productPriceIncludingTax =
                round($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue());

            if ($productQuantity <= $exportQuantityMoreThan or $productPriceIncludingTax == 0) {
                $message =
                    $productPriceIncludingTax <= 0 ? 'Product price <= 0, skipping, product id: ' . $product->getId()
                        . "price: $productPriceIncludingTax"
                        : 'Product QTY is not enough to export product id:' . $product->getId()
                        . ",qty: $productQuantity";
                $this->bidorbuyHelper->getCore()->logInfo($message);
                continue;
            }

            $product = $this->buildExportProduct($product, $categoriesMatching);
            $product[bobsi\Tradefeed::NAME_PRODUCT_ALLOW_OFFERS] = (bool)$allowOffersCategoriesMatching;
            $product[bobsi\Tradefeed::NAME_PRODUCT_CONDITION] = $this->bidorbuyHelper->getCore()
                ->getSettings()->getProductCondition($productCategories);

            if ((int)$product[bobsi\Tradefeed::NAME_PRODUCT_PRICE] <= 0) {
                $this->bidorbuyHelper->getCore()->logInfo(
                    'Product price <= 0, skipping, product id: ' . $product[bobsi\Tradefeed::NAME_PRODUCT_ID]
                );
                continue;
            }

            $exportProducts[] = $product;
        }

        return $exportProducts;
    }

    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function buildExportProduct(\Magento\Catalog\Model\Product $product, $categoriesMatching)
    {
        // phpcs:enable

        $exportedProduct = [];
        $parent = false;

        if ($product->getTypeId() == 'simple') {
            $parentIds =$this->groupedProductModel->getParentIdsByChild($product->getId());
            if (!$parentIds) {
                $parentIds = $this->configurableProductModel->getParentIdsByChild($product->getId());
            }

            if (isset($parentIds[0])) {
                $parent = $this->productModel->load($parentIds[0]);
            }
        }

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_ID] = $product->getId();
        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_NAME] = $product->getName();

        $productCodeId = $parent ? $parent->getId() . '-' . $product->getId() : $product->getId();
        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_CODE] = $product->getSku() ?: $productCodeId;

        $categoriesBreadcrumbs = [];
        foreach ($categoriesMatching as $catId) {
            $categoriesBreadcrumbs[] = $this->getBreadcrumb($catId);
        }

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_CATEGORY] = implode(
            bobsi\Tradefeed::CATEGORY_NAME_DELIMITER,
            $categoriesBreadcrumbs
        );

        //Regular price
        $priceWithoutReduct = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
        //Regular price + Taxes + Discounts
        $priceFinal = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        $currentCurrencyCode = $this->storeModel->getCurrentCurrencyCode();
        $exportCurrency = $this->bidorbuyHelper->getModuleConfig(
            BidorbuyHelper::SETTINGS_GROUP_EXPORT_CONFIGURATION,
            BidorbuyHelper::SETTINGS_FIELD_EXPORT_CURRENCY
        );

        if ($exportCurrency) {
            $priceWithoutReduct =$this->currencyConverter->currencyConvert(
                $priceWithoutReduct,
                $currentCurrencyCode,
                $exportCurrency
            ); //Regular price
            $priceFinal = $this->currencyConverter->currencyConvert(
                $priceFinal,
                $currentCurrencyCode,
                $exportCurrency
            ); //Regular price + Taxes + Discounts
        }

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_PRICE] = $priceFinal;
        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_MARKET_PRICE] =
            ($priceFinal < $priceWithoutReduct) ? $priceWithoutReduct : '';

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_SHIPPING_CLASS] = !empty($this->shippingMethodsTitles)
            ? implode(', ', $this->shippingMethodsTitles) : null;

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_SUMMARY] = $this->catalogOutputHelper->productAttribute(
            $product,
            $product->getShortDescription(),
            'shortDescription'
        );

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_DESCRIPTION] = $this->catalogOutputHelper->productAttribute(
            $product,
            $product->getDescription(),
            'description'
        );

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_ATTRIBUTES] = $this->getAvailableAttributes($product);

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_AVAILABLE_QTY] = $this->calcProductQuantity(
            $product,
            $this->bidorbuyHelper->getCore()->getSettings()->getDefaultStockQuantity()
        );

        $imagePath = $product->getImage();
        $imageUrl = (!$imagePath || $imagePath == 'no_selection')
            ? '' : $this->productMediaConfig->getMediaUrl($imagePath);

        if ($parent && !$imageUrl) {
            $imagePath = $parent->getImage();
            $imageUrl =
                (!$imagePath || $imagePath == 'no_selection') ? '' : $this->productMediaConfig->getMediaUrl($imagePath);
        }

        $images = [];

        if ($imageUrl) {
            $images[] = $imageUrl;
        }

        $mediaGalleryImages = $product->getMediaGalleryImages() ?: [];
        foreach ($mediaGalleryImages as $image) {
            $images[] = $image->getUrl();
        }

        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_IMAGE_URL] = $imageUrl;
        $exportedProduct[bobsi\Tradefeed::NAME_PRODUCT_IMAGES] = $images;

        return $exportedProduct;
    }

    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function getAvailableAttributes(\Magento\Catalog\Model\Product $product)
    {
        // phpcs:enable
        $attributes = $product->getAttributes();
        $attributesKeys = array_keys($attributes);

        $attributesOutputArray = [];

        if (in_array('manufacturer', $attributesKeys)) {
            $attributesOutputArray['Brand'] = $product->getAttributeText('manufacturer');
        }

        if (in_array('weight', $attributesKeys)) {
            $weight = $product->getWeight();
            if (is_numeric($weight) && $weight > 0) {
                $attributesOutputArray[bobsi\Tradefeed::NAME_PRODUCT_ATTR_SHIPPING_WEIGHT] =
                    number_format($weight, 2, '.', '') . $this->weightUnit;
            }
        }

        foreach ($attributes as $attribute) {
            if ($this->isAttributeUsedInFeed($attribute)) {
                $attributeValue = $product->getResource()
                    ->getAttribute($attribute->getAttributeCode())
                    ->getFrontend()
                    ->getValue($product);
                //manufacturer was added as `Brand` before
                if ($attributeValue
                    && strtolower($attributeValue) != 'no'
                    && $attribute->getAttributeCode() != 'manufacturer'
                ) {
                    $frontendLabel = $attribute->getFrontendLabel();
                    $attributesOutputArray[$frontendLabel] = $attributeValue;
                }
            }
        }

        return $attributesOutputArray;
    }

    public function calcProductQuantity($product, $default = 0)
    {
        $this->stockItemModel->load($product->getId(), 'product_id');

        return ($this->stockItemModel->getManageStock()) ? (($this->stockItemModel->getIsInStock())
            ? $this->stockItemModel->getQty() : 0) : $default;
    }

    public function isAttributeUsedInFeed($attribute)
    {
        return $attribute->getIsUserDefined()
            && is_numeric($attribute->getData(BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME))
            && (int)$attribute->getData(BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME) > 0;
    }

    public function isAttributeUsedInProductTitle($attribute)
    {
        return $attribute->getIsUserDefined()
            && is_numeric($attribute->getData(BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME))
            && (int)$attribute->getData(BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME) > 0;
    }

    public function getShippingMethodsTitles()
    {
        $methods = $this->shippingConfigModel->getActiveCarriers();

        $options = [];
        $methods = array_keys($methods);

        foreach ($methods as $code) {
            $options[] = $this->bidorbuyHelper->getScopeConfig()->getValue("carriers/$code/title") ?: $code;
        }

        return $options;
    }

    public function getWeightUnit()
    {
        return $this->bidorbuyHelper->getScopeConfig()
            ->getValue(self::XML_PATH_WEIGHT_UNIT, ScopeInterface::SCOPE_STORE);
    }
}
