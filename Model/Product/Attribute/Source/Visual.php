<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model\Product\Attribute\Source;

use Ezzar\VisualLayers\Model\ResourceModel\Visual\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Visual extends AbstractSource
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function getAllOptions(): array
    {
        if ($this->_options === null) {
            $this->_options = [['label' => __('No visual layer'), 'value' => '']];

            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('is_active', 1)
                ->setOrder('title', 'ASC');

            foreach ($collection as $visual) {
                $this->_options[] = [
                    'label' => (string) $visual->getData('title'),
                    'value' => (string) $visual->getId(),
                ];
            }
        }

        return $this->_options;
    }
}
