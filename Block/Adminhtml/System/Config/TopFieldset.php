<?php

namespace Bidorbuy\StoreIntegrator\Block\Adminhtml\System\Config;

use Bidorbuy\StoreIntegrator\Helper\BidorbuyHelper;
use Com\ExtremeIdea\Bidorbuy\StoreIntegrator\Core\Warnings;
use Magento\Framework\View\Element\Template;

class TopFieldset extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $bidorbuyHelper;
    // phpcs:enable

    public function __construct(Template\Context $context, BidorbuyHelper $bidorbuyHelper, array $data = [])
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
        $this->bidorbuyHelper->reinitCore();
        $this->setTemplate('system/config/fieldset/hint.phtml');
        return $this->toHtml();
    }

    public function getWarnings()
    {
        // phpcs:disable MEQP2.Classes.ObjectInstantiation.FoundDirectInstantiation
        // Get Warning instance from core
        $warningsCoreObject = new Warnings();
        // php:enable
        return array_merge(
            $this->bidorbuyHelper->getCore()->getWarnings(),
            $warningsCoreObject->getBusinessWarnings()
        );
    }
}
