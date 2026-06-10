<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Block\Adminhtml\Visual\Edit;

use Ezzar\VisualLayers\Model\ResourceModel\Layer\Collection;
use Ezzar\VisualLayers\Model\ResourceModel\Layer\CollectionFactory;
use Ezzar\VisualLayers\Model\Visual;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Form extends Template
{
    public function __construct(
        Context $context,
        private readonly Registry $registry,
        private readonly CollectionFactory $layerCollectionFactory,
        private readonly StoreManagerInterface $storeManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getVisual(): Visual
    {
        return $this->registry->registry('current_visual_layer');
    }

    public function getLayers(): Collection
    {
        $visualId = (int) $this->getVisual()->getId();
        $collection = $this->layerCollectionFactory->create();

        if ($visualId) {
            $collection->addFieldToFilter('visual_id', $visualId)
                ->setOrder('sort_order', 'ASC')
                ->setOrder('layer_id', 'ASC');
        } else {
            $collection->addFieldToFilter('layer_id', 0);
        }

        return $collection;
    }

    public function getSaveUrl(): string
    {
        return $this->getUrl('*/*/save');
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/index');
    }

    public function getImageUrl(?string $image): string
    {
        if (!$image) {
            return '';
        }

        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
            . ltrim($image, '/');
    }
}
