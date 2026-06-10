<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Controller\Adminhtml\Visual;

use Ezzar\VisualLayers\Model\ImageUploader;
use Ezzar\VisualLayers\Model\LayerFactory;
use Ezzar\VisualLayers\Model\VisualFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    public const ADMIN_RESOURCE = 'Ezzar_VisualLayers::visuals';

    public function __construct(
        Context $context,
        private readonly VisualFactory $visualFactory,
        private readonly LayerFactory $layerFactory,
        private readonly ImageUploader $imageUploader,
        private readonly ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        $visualId = (int) ($post['visual_id'] ?? 0);
        $redirect = $this->resultRedirectFactory->create();
        $connection = $this->resourceConnection->getConnection();
        $transactionStarted = false;

        try {
            $visual = $this->visualFactory->create();
            if ($visualId) {
                $visual->load($visualId);
                if (!$visual->getId()) {
                    throw new LocalizedException(__('This visual layer no longer exists.'));
                }
            }

            $image = $this->imageUploader->upload('image') ?: (string) ($post['existing_image'] ?? '');
            if ($image === '') {
                throw new LocalizedException(__('Please upload a layered image.'));
            }

            $title = $this->trimToLength($post['title'] ?? '', 255);
            $visual->addData([
                'title' => $title,
                'frontend_title' => $this->trimToLength($post['frontend_title'] ?? '', 255),
                'image' => $image,
                'image_alt' => $this->trimToLength($post['image_alt'] ?? '', 255),
                'is_active' => (int) ($post['is_active'] ?? 0),
            ]);
            if ($title === '') {
                throw new LocalizedException(__('Please enter a title.'));
            }

            $connection->beginTransaction();
            $transactionStarted = true;

            $visual->save();

            // Recreate rows as an ordered child set; layer IDs are not part of the admin contract.
            $connection->delete(
                $connection->getTableName('ezzar_visual_layer'),
                ['visual_id = ?' => (int) $visual->getId()]
            );

            foreach (($post['layers'] ?? []) as $layerData) {
                $layerTitle = is_array($layerData) ? $this->trimToLength($layerData['title'] ?? '', 255) : '';
                if ($layerTitle === '') {
                    continue;
                }

                $sortOrder = max(1, (int) ($layerData['sort_order'] ?? 1));
                $markerLabel = $this->trimToLength($layerData['marker_label'] ?? '', 32);
                $this->layerFactory->create()->addData([
                    'visual_id' => (int) $visual->getId(),
                    'sort_order' => $sortOrder,
                    'marker_label' => $markerLabel !== '' ? $markerLabel : (string) $sortOrder,
                    'title' => $layerTitle,
                    'what_it_is' => trim((string) ($layerData['what_it_is'] ?? '')),
                    'what_it_does' => trim((string) ($layerData['what_it_does'] ?? '')),
                    'marker_x' => $this->normalizePercent($layerData['marker_x'] ?? 50),
                    'marker_y' => $this->normalizePercent($layerData['marker_y'] ?? 50),
                    'is_active' => (int) ($layerData['is_active'] ?? 0),
                ])->save();
            }

            $connection->commit();
            $transactionStarted = false;

            $this->messageManager->addSuccessMessage(__('The visual layer has been saved.'));
            return $redirect->setPath('*/*/edit', ['visual_id' => (int) $visual->getId()]);
        } catch (\Throwable $e) {
            if ($transactionStarted) {
                $connection->rollBack();
            }

            $this->messageManager->addErrorMessage($e->getMessage());
            return $redirect->setPath('*/*/edit', $visualId ? ['visual_id' => $visualId] : []);
        }
    }

    private function normalizePercent(mixed $value): float
    {
        return max(0, min(100, (float) $value));
    }

    private function trimToLength(mixed $value, int $length): string
    {
        return substr(trim((string) $value), 0, $length);
    }
}
