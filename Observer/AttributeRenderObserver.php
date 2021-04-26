<?php

namespace Bidorbuy\StoreIntegrator\Observer;

use Bidorbuy\StoreIntegrator\Classes\BidorbuyConstants;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class AttributeRenderObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {

        $attrType = $observer->getEvent()->getAttribute()->getFrontendInput();
        $model = $observer->getEvent()->getAttribute()->getSourceModel()
            ?: $observer->getEvent()->getAttribute()->getBackendModel();

        $disabledUsedInTitleField = isset($model)
        && (strpos($model, 'Backend\Array')
            || strpos($model, 'Source\Boolean')) ? true : false;

        if (isset($attrType) && is_string($attrType) && strpos($attrType, 'select') === false) {
            return;
        }

        $fieldset = $observer->getForm()->addField(
            'bidorbuy',
            'fieldset',
            ['legend' => __('bidorbuy Feed')]
        );

        $fieldset->addField(
            BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME,
            'select',
            [
                'name' => BidorbuyConstants::ATTR_IS_USED_IN_FEED_NAME,
                'label' => __('Used for bidorbuy Feed'),
                'values' => ['1' => 'Yes', '0' => 'No']
            ]
        );
        $fieldset->addField(
            BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
            'select',
            [
                'name' => BidorbuyConstants::ATTR_IS_USED_IN_PRODUCT_TITLE_NAME,
                'label' => __('Used in title of a Product'),
                'values' => ['1' => 'Yes', '0' => 'No'],
                'disabled' => $disabledUsedInTitleField ? 'disabled' : ''
            ]
        );
    }
}
