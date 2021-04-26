<?php

namespace Bidorbuy\StoreIntegrator\Observer;

use Bidorbuy\StoreIntegrator\Classes\BidorbuyConstants;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AttributeLoadDataObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $collection = $observer->getCollection();

        if (isset($collection) && is_a($collection, 'Mage_Index_Model_Resource_Process_Collection')) {
            $collection->addExpressionFieldToSelect(BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME, '', []);
            $collection->addExpressionFieldToSelect(BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME, '', []);
        }
    }
}
