<?php

namespace Bidorbuy\StoreIntegrator\Observer;

use Bidorbuy\StoreIntegrator\Classes\BidorbuyConstants;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AttributeAddToGridObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $block = $observer->getBlock();
        $targetBlock = 'Magento\Catalog\Block\Adminhtml\Product\Attribute\Grid';
        if ($block->getType() == $targetBlock) {
            $block->addColumn(
                BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME,
                [
                    'header' => __('Used for bidorbuy Feed'),
                    'index' => BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME,
                    'filter' => false,
                    'type' => 'options',
                    'options' => ['1' => __('Yes'), '0' => __('No')],
                    'align' => 'center',
                ]
            );
            $block->addColumn(
                BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
                [
                    'header' => __('Used in title of a Product'),
                    'index' => BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
                    'filter' => false,
                    'type' => 'options',
                    'options' => ['1' => __('Yes'), '0' => __('No')],
                    'align' => 'center'
                ]
            );
        }
    }
}
