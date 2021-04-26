<?php

namespace Bidorbuy\StoreIntegrator\Observer;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Bidorbuy\StoreIntegrator\Helper\DataHelper;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Serialize;

class ModuleConfigObserver implements ObserverInterface
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $request;
    protected $bidorbuyHelper;
    protected $dataHelper;
    protected $messageManager;
    protected $serializer;

    // phpcs:enable

    public function __construct(
        Http $request,
        BidorbuyHelper $bidorbuyHelper,
        ManagerInterface $messageManager,
        DataHelper $dataHelper,
        Serialize $serializer
    ) {
        $this->request = $request;
        $this->bidorbuyHelper = $bidorbuyHelper;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
        $this->serializer = $serializer;
    }

    /**
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        // phpcs:enable

        $groups = $this->request->getParam('groups', []);
        $settings = [];
        foreach ($groups as $group) {
            foreach ($group['fields'] as $key => $value) {
                $settings[$key] = $value['value'];
            }
        }

        if (empty($settings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES])) {
            $settings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES] = [];
        }

        if (empty($settings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES])) {
            $settings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES] = [];
        }

        $settings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES] = $this->dataHelper->getExportCategoriesIds(
            $settings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES]
        );

        $settings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES] = $this->dataHelper->
        getAllowOffersCategoriesIds($settings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES]);

        $settings[bobsi\Settings::NAME_PRODUCT_CONDITION_SECONDHAND_CATEGORIES] =
            $this->dataHelper->getProductConditionCategoriesIds(
                $settings[bobsi\Settings::NAME_PRODUCT_CONDITION_SECONDHAND_CATEGORIES]
            );

        $settings[bobsi\Settings::NAME_PRODUCT_CONDITION_REFURBISHED_CATEGORIES] =
            $this->dataHelper->getProductConditionCategoriesIds(
                $settings[bobsi\Settings::NAME_PRODUCT_CONDITION_REFURBISHED_CATEGORIES]
            );

        $this->bidorbuyHelper->getCore()->getSettings()->unserialize($this->serializer->serialize($settings));

        $value = $this->bidorbuyHelper->getCore()->getSettings()->serialize(true);
        $this->bidorbuyHelper->saveModuleEncodedConfiguration($value);

        // reset tokens
        if ($this->request->getParam(bobsi\Settings::NAME_ACTION_RESET)) {
            $this->bidorbuyHelper->getCore()->processAction(bobsi\Settings::NAME_ACTION_RESET);

            $value = $this->bidorbuyHelper->getCore()->getSettings()->serialize(true);
            $this->bidorbuyHelper->saveModuleEncodedConfiguration($value);

            $this->messageManager->addSuccess('Tokens are successfully reset.');
        }

        // download/remove logs
        if ($this->request->getParam(bobsi\Settings::NAME_LOGGING_FORM_ACTION)) {
            $data = [
                bobsi\Settings::NAME_LOGGING_FORM_FILENAME =>
                    ($this->request->getParam(bobsi\Settings::NAME_LOGGING_FORM_FILENAME))
                        ? $this->request->getParam(bobsi\Settings::NAME_LOGGING_FORM_FILENAME) : ''
            ];

            $result = $this->bidorbuyHelper->getCore()->processAction(
                $this->request->getParam(bobsi\Settings::NAME_LOGGING_FORM_ACTION),
                $data
            );
            // clear previous messages if they exist.
            $this->messageManager->getMessages(true);

            foreach ($result as $warn) {
                $this->messageManager->addSuccess($warn);
            }
        }
    }
}
