<?php

namespace Bidorbuy\StoreIntegrator\Model\Config\Source;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;

class LoggingApplication implements \Magento\Framework\Option\ArrayInterface
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    // phpcs:enable

    public function __construct(BidorbuyHelper $bidorbuyHelper)
    {
        $this->bidorbuyHelper = $bidorbuyHelper;
    }

    public function toOptionArray()
    {
        $arrayToReturn = [];
        $options = $this->bidorbuyHelper->getCore()->getSettings()->getLoggingApplicationOptions();
        foreach ($options as $key => $label) {
            $arrayToReturn[] = [
                'value' => $key,
                'label' => __($label)
            ];
        }

        return $arrayToReturn;
    }
}
