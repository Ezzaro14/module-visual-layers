<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Controller\Adminhtml\Visual;

use Ezzar\VisualLayers\Model\VisualFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Delete extends Action
{
    public const ADMIN_RESOURCE = 'Ezzar_VisualLayers::visuals';

    public function __construct(
        Context $context,
        private readonly VisualFactory $visualFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('visual_id');
        $redirect = $this->resultRedirectFactory->create();

        try {
            if (!$this->getRequest()->isPost()) {
                throw new LocalizedException(__('Invalid delete request.'));
            }

            if (!$id) {
                throw new LocalizedException(__('Missing visual ID.'));
            }

            $visual = $this->visualFactory->create()->load($id);
            if (!$visual->getId()) {
                throw new LocalizedException(__('This visual layer no longer exists.'));
            }

            $visual->delete();
            $this->messageManager->addSuccessMessage(__('The visual layer has been deleted.'));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $redirect->setPath('*/*/index');
    }
}
