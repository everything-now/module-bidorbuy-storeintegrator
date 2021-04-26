<?php

namespace Bidorbuy\StoreIntegrator\Controller\Adminhtml\Category;

class CategoriesJson extends \Magento\Catalog\Controller\Adminhtml\Category
{
    // phpcs:disable MEQP2.PHP.ProtectedClassMember.FoundProtected
    protected $resultJsonFactory;
    protected $layoutFactory;
    // phpcs:enable

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $categoryId = (int)$this->getRequest()->getPost('id');
        $this->getRequest()->setParam('id', $categoryId);
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setJsonData(
            $this->layoutFactory->create()->createBlock('Magento\Catalog\Block\Adminhtml\Category\Tree')->getTreeJson(
                $categoryId
            )
        );
    }
}
