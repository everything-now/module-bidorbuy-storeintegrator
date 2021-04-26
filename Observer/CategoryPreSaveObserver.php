<?php

namespace Bidorbuy\StoreIntegrator\Observer;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Event\Observer;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;

class CategoryPreSaveObserver implements ObserverInterface
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    protected $serializer;
    // phpcs:enable

    public function __construct(BidorbuyHelper $bidorbuyHelper, Serialize $serializer)
    {
        $this->bidorbuyHelper = $bidorbuyHelper;
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
        $request = $observer->getData('request');
        $params = $request->getParam('general');
        $isActive = isset($params['is_active']) && $params['is_active'];
        if (!$isActive) {
            $category = $observer->getData('category');
            $categoriesIds = $category->getAllChildren(true);
            $bobCategories = $this->bidorbuyHelper->getModuleConfig(
                BidorbuyHelper::SETTINGS_GROUP_EXPORT_CRITERIA,
                BidorbuyHelper::SETTINGS_FIELD_EXCLUDED_CATEGORIES
            );
            $bobCategories = explode(',', $bobCategories);

            $bobCategoriesValue = implode(',', array_diff($bobCategories, $categoriesIds));
            $this->bidorbuyHelper->saveModuleConfiguration(
                BidorbuyHelper::SETTINGS_GROUP_EXPORT_CRITERIA,
                BidorbuyHelper::SETTINGS_FIELD_EXCLUDED_CATEGORIES,
                $bobCategoriesValue
            );

            $bobAllowOffersCategories = $this->bidorbuyHelper->getModuleConfig(
                BidorbuyHelper::SETTINGS_GROUP_PRODUCT_SETTINGS,
                BidorbuyHelper::SETTINGS_FIELD_INCLUDED_ALLOW_OFFERS_CATEGORIES
            );
            $bobAllowOffersCategories = explode(',', $bobAllowOffersCategories);
            $bobAllowOffersCategoriesValue = implode(',', array_diff($bobAllowOffersCategories, $categoriesIds));
            $this->bidorbuyHelper->saveModuleConfiguration(
                BidorbuyHelper::SETTINGS_GROUP_PRODUCT_SETTINGS,
                BidorbuyHelper::SETTINGS_FIELD_INCLUDED_ALLOW_OFFERS_CATEGORIES,
                $bobAllowOffersCategoriesValue
            );

            $bobEncodedSettings = $this->bidorbuyHelper->getModuleConfig(
                BidorbuyHelper::SETTINGS_GROUP_EXPORT_CONFIGURATION,
                BidorbuyHelper::SETTINGS_FIELD_ENCODED_CONFIGURATION
            );

            // phpcs:disable MEQP1.Security.DiscouragedFunction.Found
            $bobDecodedSettings = $this->serializer->unserialize(base64_decode($bobEncodedSettings));
            // phpcs:enable
            $bobDecodedSettings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES] = array_diff(
                $bobDecodedSettings[bobsi\Settings::NAME_EXCLUDE_CATEGORIES],
                $categoriesIds
            );
            $bobDecodedSettings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES] = array_diff(
                $bobDecodedSettings[bobsi\Settings::NAME_INCLUDE_ALLOW_OFFERS_CATEGORIES],
                $categoriesIds
            );
            $this->bidorbuyHelper->getCore()->getSettings()->unserialize(
                $this->serializer->serialize($bobDecodedSettings)
            );
            $value = $this->bidorbuyHelper->getCore()->getSettings()->serialize(true);
            $this->bidorbuyHelper->saveModuleEncodedConfiguration($value);
        }
    }
}
