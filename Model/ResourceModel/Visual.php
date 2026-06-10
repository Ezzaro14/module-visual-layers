<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Visual extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('ezzar_visual_layer_visual', 'visual_id');
    }
}
