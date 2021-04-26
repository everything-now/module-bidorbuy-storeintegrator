<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Backend\Block\Template\Context;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Config\Block\System\Config\Form\Field;

class TokenExportUrl extends Field
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
     * phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // phpcs:enable
        $tokenExport = $this->bidorbuyHelper->getCore()->getSettings()->getTokenExport();

        $url = $this->bidorbuyHelper->getUrlInterface()->getRouteUrl(
            'bidorbuy/storeintegrator/index',
            [
                'action' => 'export',
                bobsi\Settings::PARAM_TOKEN => $tokenExport
            ]
        );
        $html = '
            <input type="hidden" name="groups[links][fields][tokenExportUrl][value]" 
            id="bidorbuystoreintegrator_links_tokenExportUrl"
                value="' . $tokenExport . '">
            <input type="text" readonly="readonly" name="tokenExportUrl" id="tokenExportUrl" value="' . $url . '" />
            <button type="button" class="button launch-button">' . __('Launch') . '</button>
            <button type="button" class="button copy-button">' . __('Copy') . '</button>
            </td>';
        return $html;
    }
}
