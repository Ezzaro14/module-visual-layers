<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Controller\Adminhtml\Visual;

use Ezzar\VisualLayers\Model\VisualFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Edit extends Action
{
    public const ADMIN_RESOURCE = 'Ezzar_VisualLayers::visuals';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly Registry $registry,
        private readonly VisualFactory $visualFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('visual_id');
        $visual = $this->visualFactory->create();

        if ($id) {
            $visual->load($id);
            if (!$visual->getId()) {
                $this->messageManager->addErrorMessage(__('This visual layer no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/index');
            }
        }

        $this->registry->register('current_visual_layer', $visual);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ezzar_VisualLayers::visuals');
        $resultPage->getConfig()->getTitle()->prepend(
            $visual->getId() ? __('Edit Visual Layer') : __('New Visual Layer')
        );

        return $resultPage;
    }
}
