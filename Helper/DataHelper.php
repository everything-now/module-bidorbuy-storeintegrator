<?php

namespace Bidorbuy\StoreIntegrator\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Helper\AbstractHelper;

class DataHelper extends AbstractHelper
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $categoryModel;
    // phpcs:enable

    public function __construct(Context $context, Category $categoryModel)
    {
        parent::__construct($context);

        $this->categoryModel = $categoryModel;
    }

    public function getExportCategoriesIds($ids = [])
    {
        // try to explode if $ids is string
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (!is_array($ids)) {
            $ids = [];
        }

        $categoriesIds = array_merge(
            $this->categoryModel->getTreeModel()
                ->load()
                ->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->getAllIds(),
            ['0']
        );
        return array_values(array_diff($categoriesIds, $ids));
    }

    public function getAllowOffersCategoriesIds($ids)
    {
        // try to explode if $ids is string
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (!is_array($ids)) {
            $ids = [];
        }

        return $ids;
    }

    public function getProductConditionCategoriesIds($ids)
    {
        if ($ids === "") {
            return [];
        }

        if (is_string($ids)) {
            return explode(',', $ids);
        }

        return $ids;
    }
}
