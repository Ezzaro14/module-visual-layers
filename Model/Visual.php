<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Model;

use Magento\Framework\Model\AbstractModel;

class Visual extends AbstractModel
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel\Visual::class);
    }
}
