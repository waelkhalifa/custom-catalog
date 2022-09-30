<?php


namespace Wael\CustomCatalog\Block\Adminhtml\Product\Edit\Button;

/**
 * Class Back
 * @package Wael\CustomCatalog\Block\Adminhtml\Product\Edit\Button
 */
class Back extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('customcatalog/product/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
