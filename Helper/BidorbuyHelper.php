<?php

namespace Bidorbuy\StoreIntegrator\Helper;

use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core\Core;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core\Version;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;

class BidorbuyHelper extends AbstractHelper
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected static $core = null;
    protected $storeManager;
    protected $configWriter;
    protected $productMetadata;
    protected $context;
    protected $reinitableConfig;
    // phpcs:enable

    const SETTINGS_SECTION = 'bobsi_settings';
    const SETTINGS_GROUP_EXPORT_CONFIGURATION = 'exportConfiguration';
    const SETTINGS_GROUP_EXPORT_CRITERIA = 'exportCriteria';
    const SETTINGS_GROUP_PRODUCT_SETTINGS = 'productSettings';
    const SETTINGS_GROUP_EXPORT_LINKS = 'links';
    const SETTINGS_GROUP_EXPORT_DEBUG = 'debug';
    const SETTINGS_GROUP_EXPORT_VERSION = 'version';

    const SETTINGS_FIELD_ENCODED_CONFIGURATION = 'encodedConfiguration';
    const SETTINGS_FIELD_EXPORT_CURRENCY = 'currency';
    const SETTINGS_FIELD_EXCLUDED_CATEGORIES = 'excludeCategories';
    const SETTINGS_FIELD_INCLUDED_ALLOW_OFFERS_CATEGORIES = 'includeAllowOffersCategories';

    const SETTINGS_FIELD_BASIC_ACCESS_AUTH_USER = 'username';
    const SETTINGS_FIELD_BASIC_ACCESS_AUTH_PASSWORD = 'password';

    // phpcs:disable CustomOperationsFound
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        WriterInterface $configWriter,
        ProductMetadataInterface $productMetadata,
        ReinitableConfigInterface $reinitableConfig
    ) {
        parent::__construct($context);

        $this->context = $context;

        $this->storeManager = $storeManager;
        $this->configWriter = $configWriter;
        $this->productMetadata = $productMetadata;
        $this->reinitableConfig = $reinitableConfig;
    }

    public function getCore()
    {
        if (static::$core === null) {
            // phpcs:disable MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation
            // Get Core instance and initialise
            $core = new Core();
            // phpcs:enable
            Version::$version = '1.5.2';
            $core->init(
                $this->getStoreName(),
                $this->getStoreEmail(),
                'Magento ' . $this->getStoreVersion(),
                $this->getModuleConfig(
                    static::SETTINGS_GROUP_EXPORT_CONFIGURATION,
                    static::SETTINGS_FIELD_ENCODED_CONFIGURATION
                )
            );
            static::$core = $core;
        }

        return static::$core;
    }

    public function getStoreEmail()
    {
        return $this->getScopeConfig()->getValue('trans_email/ident_general/email', ScopeInterface::SCOPE_STORE);
    }

    public function getStoreName()
    {
        return $this->storeManager->getStore()->getName();
    }

    public function getStoreVersion()
    {
        return $this->productMetadata->getVersion();
    }

    public function getScopeConfig()
    {
        return $this->context->getScopeConfig();
    }

    public function getModuleConfig($group, $field, $storeId = null)
    {
        return $this->getScopeConfig()->getValue(
            static::SETTINGS_SECTION . "/$group/$field",
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }

    public function saveModuleEncodedConfiguration($value)
    {
        $this->configWriter->save(
            static::SETTINGS_SECTION . '/'
            . static::SETTINGS_GROUP_EXPORT_CONFIGURATION . '/'
            . static::SETTINGS_FIELD_ENCODED_CONFIGURATION,
            $value,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function saveModuleConfiguration($group, $field, $value)
    {
        $this->configWriter->save(
            static::SETTINGS_SECTION . '/' . $group . '/' . $field,
            $value,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT
        );
    }

    public function getUrlInterface()
    {
        return $this->_urlBuilder;
    }

    public function reinitCore()
    {
        $this->reinitableConfig->reinit();
        static::$core = null;
        $this->getCore();
    }
}
