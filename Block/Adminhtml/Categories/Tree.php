<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\Categories;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Catalog\Block\Adminhtml\Category\Checkboxes as Magento;

class Tree extends Magento\Tree
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    // phpcs:enable

    // phpcs:disable MEQP2.Classes.MutableObjects.MutableObjects
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Magento\Backend\Model\Auth\Session $backendSession,
        BidorbuyHelper $bidorbuyHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $categoryTree,
            $registry,
            $categoryFactory,
            $jsonEncoder,
            $resourceHelper,
            $backendSession,
            $data
        );

        $this->bidorbuyHelper = $bidorbuyHelper;
    }

    // phpcs:enable

    // phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function _prepareLayout()
    {
        // phpcs:enable

        $this->setTemplate('catalog/category/tree.phtml');
    }

    public function getCategoryIds()
    {
        $excludeCategories = $this->bidorbuyHelper->getModuleConfig(
            BidorbuyHelper::SETTINGS_GROUP_EXPORT_CRITERIA,
            BidorbuyHelper::SETTINGS_FIELD_EXCLUDED_CATEGORIES
        );

        $categories = explode(',', $excludeCategories);

        if (($key = array_search('1', $categories)) !== false) {
            unset($categories[$key]); //remove category 1
        }

        return $categories;
    }

    public function getLoadTreeUrl($expanded = null)
    {
        $params = [];
        $params['id'] = 0;

        if ($expanded === null && $this->_backendSession->getIsTreeWasExpanded() || $expanded == true) {
            $params['expand_all'] = true;
        }

        return $this->getUrl('bidorbuy/category/categoriesJson', $params);
    }

    public function getIdsString()
    {
        return implode(',', $this->getCategoryIds());
    }

    public function getCategoryCollection()
    {
        $storeId = $this->getRequest()->getParam('store', $this->_getDefaultStoreId());
        $collection = $this->getData('category_collection');
        if ($collection === null) {
            $collection = $this->_categoryFactory->create()->getCollection();

            $collection->addAttributeToSelect('name')
                ->addAttributeToSelect('is_active')
                ->addFieldToFilter('is_active', 1)
                ->setProductStoreId($storeId)
                ->setLoadProductCount($this->_withProductCount)
                ->setStoreId($storeId);

            $this->setData('category_collection', $collection);
        }

        return $collection;
    }
}
