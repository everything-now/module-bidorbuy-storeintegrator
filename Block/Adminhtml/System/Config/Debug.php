<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Config\Block\System\Config\Form\Fieldset;

class Debug extends Fieldset
{

    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    // phpcs:enable

    // phpcs:disable MEQP2.Classes.MutableObjects.MutableObjects
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        BidorbuyHelper $bidorbuyHelper,
        array $data = []
    ) {
        parent::__construct($context, $authSession, $jsHelper, $data);

        $this->bidorbuyHelper = $bidorbuyHelper;
    }
    // phpcs:enable

    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function _getFooterHtml($element)
    {
        // phpcs:enable

        $html = '</tbody></table>';
        $logsHtml = preg_replace('/<form[^>]+\>/i', '', $this->bidorbuyHelper->getCore()->getLogsHtml());
        $logsHtml = str_replace('</form>', '', $logsHtml);
        $html .= $logsHtml;
        $html .= '</fieldset>' . $this->_getExtraJs($element);
        $html .= $element->getIsNested() ? '</td></tr>' : '</div>';

        return $html;
    }
}
