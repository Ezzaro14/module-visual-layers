<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Controller\Adminhtml\Visual;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'Ezzar_VisualLayers::visuals';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ezzar_VisualLayers::visuals');
        $resultPage->getConfig()->getTitle()->prepend(__('Visual Layers'));

        return $resultPage;
    }
}
