<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model\ResourceModel\Layer;

use Ezzar\VisualLayers\Model\Layer as LayerModel;
use Ezzar\VisualLayers\Model\ResourceModel\Layer;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(LayerModel::class, Layer::class);
    }
}
