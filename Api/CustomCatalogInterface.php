<?php


namespace Wael\CustomCatalog\Api;

/**
 * Interface CustomCatalogInterface
 * @api
 * @package Wael\CustomCatalog\Api
 */
interface CustomCatalogInterface
{
    /**
     * Get products by VPN
     *
     * @param  string  $vpn
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @api
     */
    public function getByVPN($vpn);

    /**
     * Update product
     *
     * @param  mixed  $product
     * @return void
     * @api
     */
    public function update($product);
}
