<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\Categories;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Catalog\Block\Adminhtml\Category\Checkboxes as Magento;

class AllowOffersTree extends Tree
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
            $bidorbuyHelper,
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

        $this->setTemplate('catalog/category/allow-offers-tree.phtml');
    }

    public function getCategoryIds()
    {
        $includeAllowOffersCategories = $this->bidorbuyHelper->getModuleConfig(
            BidorbuyHelper::SETTINGS_GROUP_PRODUCT_SETTINGS,
            BidorbuyHelper::SETTINGS_FIELD_INCLUDED_ALLOW_OFFERS_CATEGORIES
        );

        $categories = explode(',', $includeAllowOffersCategories);

        if (($key = array_search('1', $categories)) !== false) {
            unset($categories[$key]); //remove category 1
        }

        return $categories;
    }
}
