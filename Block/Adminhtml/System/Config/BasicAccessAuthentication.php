<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;

class BasicAccessAuthentication extends Field
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
        //phpcs:enable
        $userName = $this->bidorbuyHelper->getModuleConfig(
            BidorbuyHelper::SETTINGS_GROUP_EXPORT_DEBUG,
            BidorbuyHelper::SETTINGS_FIELD_BASIC_ACCESS_AUTH_USER
        );
        $password = $this->bidorbuyHelper->getModuleConfig(
            BidorbuyHelper::SETTINGS_GROUP_EXPORT_DEBUG,
            BidorbuyHelper::SETTINGS_FIELD_BASIC_ACCESS_AUTH_PASSWORD
        );
        $html = "
             <tr>
                <td><b>Basic Access Authentication</b><br/>(if necessary)</td>
             </tr>
             <tr>
                 <td colspan='3'>
                     <br/>
                     <span style='color: red'> 
                        Do not enter username or password of ecommerce platform, 
                        please read carefully about this kind of authentication!
                    </span>
                 </td>
             </tr>
             
            <tr id='row_bidorbuystoreintegrator_debug_username'>
                <td class='label'><label for='bidorbuystoreintegrator_debug_username'> Username</label></td>
                <td class='value'><input id='bidorbuystoreintegrator_debug_username' 
                    name='groups[debug][fields][username][value]' class=' input-text' type='text' value='$userName'>
                    <p class='note'>
                        <span>
                          Please specify the username if your platform is protected by 
                          <a href='http://en.wikipedia.org/wiki/Basic_access_authentication' 
                          target='_blank'>Basic Access Authentication</a>
                     </span>
                 </p>
            </td>
            </tr>
                            
            <tr id='row_bidorbuystoreintegrator_debug_password'>
                <td class='label'><label for='bidorbuystoreintegrator_debug_password'> Password</label></td>
                <td class='value'><input id='bidorbuystoreintegrator_debug_password' 
                name='groups[debug][fields][password][value]'  class=' validate-password input-text' 
                type='password' value='$password'>
                   <p class='note'>
                       <span>
                           Please specify the password if your platform is protected by 
                           <a href='http://en.wikipedia.org/wiki/Basic_access_authentication' 
                           target='_blank'>Basic Access Authentication</a>
                       </span>
                   </p>
                </td>
            </tr>         
             ";

        return $this->getRequest()->getParam('baa', false) ? $html : '';
    }
}
