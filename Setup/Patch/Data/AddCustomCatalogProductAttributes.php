<?php


namespace Wael\CustomCatalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class AddCustomCatalogProductAttributes
 * @package Wael\CustomCatalog\Setup\Patch\Data
 */
class AddCustomCatalogProductAttributes implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var EavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param  ModuleDataSetupInterface  $moduleDataSetup
     * @param  EavSetupFactory  $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @return DataPatchInterface|void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->addAttribute(
            Product::ENTITY,
            'copy_write_info',
            [
                'group' => 'General',
                'type' => 'text',
                'label' => 'Copy Write Info',
                'input' => 'text',
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'user_defined' => false,
                'visible' => true,
                'searchable' => false,
                'filterable' => false,
                'required' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                'note' => 'custom catalog copy write info',
            ]
        );
        $eavSetup->addAttribute(
            Product::ENTITY,
            'vpn',
            [
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'VPN',
                'input' => 'text',
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'user_defined' => false,
                'visible' => true,
                'searchable' => false,
                'filterable' => false,
                'required' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_visible_in_grid' => true,
                'is_filterable_in_grid' => true,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                'note' => 'custom catalog vpn',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
