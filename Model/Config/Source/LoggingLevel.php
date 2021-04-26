<?php

namespace Bidorbuy\StoreIntegrator\Model\Config\Source;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;

class LoggingLevel implements \Magento\Framework\Option\ArrayInterface
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
        $options = $this->bidorbuyHelper->getCore()->getSettings()->getLoggingLevelOptions();
        foreach ($options as $key) {
            $arrayToReturn[] = [
                'value' => $key,
                'label' => __(ucfirst($key))
            ];
        }

        return $arrayToReturn;
    }
}
