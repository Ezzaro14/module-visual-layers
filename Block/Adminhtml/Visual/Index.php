<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Block\Adminhtml\Visual;

use Ezzar\VisualLayers\Model\ResourceModel\Visual\Collection;
use Ezzar\VisualLayers\Model\ResourceModel\Visual\CollectionFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class Index extends Template
{
    public function __construct(
        Context $context,
        private readonly CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getVisuals(): Collection
    {
        return $this->collectionFactory->create()->setOrder('updated_at', 'DESC');
    }

    public function getNewUrl(): string
    {
        return $this->getUrl('*/*/edit');
    }

    public function getEditUrl(int $visualId): string
    {
        return $this->getUrl('*/*/edit', ['visual_id' => $visualId]);
    }

    public function getDeleteUrl(int $visualId): string
    {
        return $this->getUrl('*/*/delete', ['visual_id' => $visualId]);
    }
}
