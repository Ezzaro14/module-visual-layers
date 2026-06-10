<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\ViewModel;

use Ezzar\VisualLayers\Model\ResourceModel\Layer\Collection;
use Ezzar\VisualLayers\Model\ResourceModel\Layer\CollectionFactory as LayerCollectionFactory;
use Ezzar\VisualLayers\Model\Visual;
use Ezzar\VisualLayers\Model\VisualFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class VisualLayers implements ArgumentInterface
{
    public function __construct(
        private readonly Registry $registry,
        private readonly VisualFactory $visualFactory,
        private readonly LayerCollectionFactory $layerCollectionFactory,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    public function getCurrentProduct(): ?ProductInterface
    {
        $product = $this->registry->registry('current_product');

        return $product instanceof ProductInterface ? $product : null;
    }

    public function getVisual(?ProductInterface $product = null): ?Visual
    {
        $product ??= $this->getCurrentProduct();
        if (!$product) {
            return null;
        }

        $visualId = (int) $product->getData('visual_layer_id');
        if ($visualId <= 0) {
            return null;
        }

        $visual = $this->visualFactory->create()->load($visualId);
        if (!$visual->getId() || !(int) $visual->getData('is_active')) {
            return null;
        }

        return $visual;
    }

    public function getLayers(Visual $visual): Collection
    {
        return $this->layerCollectionFactory->create()
            ->addFieldToFilter('visual_id', (int) $visual->getId())
            ->addFieldToFilter('is_active', 1)
            ->setOrder('sort_order', 'ASC')
            ->setOrder('layer_id', 'ASC');
    }

    public function getImageUrl(Visual $visual): string
    {
        $image = (string) $visual->getData('image');
        if ($image === '') {
            return '';
        }

        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . ltrim($image, '/');
    }
}
