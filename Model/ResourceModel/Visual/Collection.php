<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model\ResourceModel\Visual;

use Ezzar\VisualLayers\Model\ResourceModel\Visual;
use Ezzar\VisualLayers\Model\Visual as VisualModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(VisualModel::class, Visual::class);
    }
}
