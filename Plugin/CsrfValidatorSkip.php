<?php
namespace Bidorbuy\StoreIntegrator\Plugin;

class CsrfValidatorSkip
{
    /**
     * @param \Magento\Framework\App\Request\CsrfValidator $subject
     * @param \Closure $proceed
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ActionInterface $action
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundValidate($subject, \Closure $proceed, $request, $action)
    {
        if ($request->getModuleName() == 'bidorbuy') {
            return;
        }

        $proceed($request, $action);
    }
}
