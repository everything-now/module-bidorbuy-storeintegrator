<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;

class ExcludeCategories extends Field
{

    /**
     * phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.Found
     * phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // phpcs:enable

        $block = $this->getLayout()->createBlock(
            'Bidorbuy\StoreIntegrator\Block\Adminhtml\Categories\Tree'
        );
        return $block->toHtml();
    }
}
