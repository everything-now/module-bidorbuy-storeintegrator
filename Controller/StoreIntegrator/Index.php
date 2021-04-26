<?php

namespace Bidorbuy\StoreIntegrator\Controller\StoreIntegrator;

use Bidorbuy\StoreIntegrator\Classes\Export;
use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    protected $exportAction;
    // phpcs:enable

    public function __construct(Context $context, BidorbuyHelper $bidorbuyHelper, Export $exportAction)
    {
        parent::__construct($context);
        $this->bidorbuyHelper = $bidorbuyHelper;
        $this->exportAction = $exportAction;
    }

    public function execute()
    {
        $request = $this->getRequest();
        $action = $request->getParam('action');
        $token = $request->getParam(bobsi\Settings::PARAM_TOKEN);
        switch ($action) {
            case 'export':
                $this->bidorbuyHelper->reinitCore();
                $ids = $this->getRequest()->getParam(bobsi\Settings::PARAM_IDS);
                $this->exportAction->export($token, $ids);
                break;
            case 'download':
                $this->bidorbuyHelper->getCore()->download($token);
                break;
            case 'downloadl':
                $this->bidorbuyHelper->getCore()->downloadl($token);
                break;
            case 'version':
                $phpInfo = 'y' == $this->getRequest()->getParam('phpinfo', 'n');
                $this->bidorbuyHelper->getCore()->showVersion($token, $phpInfo);
                break;
        }
    }
}
