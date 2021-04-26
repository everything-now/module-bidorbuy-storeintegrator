<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Backend\Block\Template\Context;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Config\Block\System\Config\Form\Field;

class Version extends Field
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    // phpcs:enable

    public function __construct(Context $context, BidorbuyHelper $bidorbuyHelper, array $data = [])
    {
        parent::__construct($context, $data);
        $this->bidorbuyHelper = $bidorbuyHelper;
    }

    /**
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // phpcs:enable
        $tokenDownload = $this->bidorbuyHelper->getCore()->getSettings()->getTokenDownload();

        $url = $this->bidorbuyHelper->getUrlInterface()->getRouteUrl(
            'bidorbuy/storeintegrator/index',
            [
                'action' => 'version',
                bobsi\Settings::PARAM_TOKEN => $tokenDownload,
                'phpinfo' => 'y'
            ]
        );
        $html = "<tr><td><a href='$url' target='_blank'>@See PHP information</a></td></tr>";
        $html .= "<tr><td>{$this->bidorbuyHelper->getCore()->getVersionInstance()->getLivePluginVersion()}</td></tr>";
        $html .= '</tbody></table>';
        return $html;
    }
}
