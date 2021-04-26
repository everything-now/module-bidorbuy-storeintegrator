<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core as bobsi;
use Magento\Config\Block\System\Config\Form\Fieldset;

class Links extends Fieldset
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected function _getFooterHtml($element)
    {
        // phpcs:enable

        $html = '</tbody></table>';
        $html .= '
                <input type="hidden" name="' . bobsi\Settings::NAME_ACTION_RESET . '" 
                id="' . bobsi\Settings::NAME_ACTION_RESET . '" value="0">
                <button id= "reset-tokens" type="submit">Reset Tokens</button>
        ';
        $html .= '</fieldset>' . $this->_getExtraJs($element);
        $html .= ($element->getIsNested()) ? '</div></td></tr>' : '</div>';

        return $html;
    }
}
