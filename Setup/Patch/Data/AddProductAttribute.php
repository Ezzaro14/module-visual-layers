<?php
declare(strict_types=1);

namespace Ezzar\VisualLayers\Setup\Patch\Data;

use Ezzar\VisualLayers\Model\Product\Attribute\Source\Visual;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddProductAttribute implements DataPatchInterface
{
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(Product::ENTITY, 'visual_layer_id', [
            'type' => 'int',
            'label' => 'Visual Layer',
            'input' => 'select',
            'source' => Visual::class,
            'required' => false,
            'sort_order' => 90,
            'global' => ScopedAttributeInterface::SCOPE_STORE,
            'group' => 'Content',
            'visible' => true,
            'user_defined' => true,
            'default' => null,
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'note' => 'Select the editable exploded visual layer for this product.',
        ]);

        $attributeId = (int) $eavSetup->getAttributeId(Product::ENTITY, 'visual_layer_id');
        if ($attributeId) {
            // Attribute set assignment is intentionally left to Magento admin.
            $this->moduleDataSetup->getConnection()->delete(
                $this->moduleDataSetup->getTable('eav_entity_attribute'),
                ['attribute_id = ?' => $attributeId]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
