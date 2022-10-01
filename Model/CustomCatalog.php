<?php


namespace Wael\CustomCatalog\Model;

/**
 * Class CustomCatalog
 * @package Wael\CustomCatalog\Model
 */
class CustomCatalog implements \Wael\CustomCatalog\Api\CustomCatalogInterface
{
    const TOPIC = 'customcatalog.product.update';
    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    private $publisher;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var  \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * CustomCatalog constructor.
     * @param  \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository
     * @param  \Magento\Framework\Api\SearchCriteriaBuilder  $searchCriteriaBuilder
     * @param  \Magento\Framework\MessageQueue\PublisherInterface  $publisher
     * @param  \Magento\Framework\Serialize\Serializer\Json  $jsonSerializer
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->publisher = $publisher;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Returns products for given VPN
     *
     * @param  string  $vpn  VPN.
     * @return \Magento\Catalog\Api\Data\ProductSearchResultsInterface
     * @api
     */
    public function getByVPN($vpn)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('vpn', $vpn, 'eq')->create();
        $products = $this->productRepository->getList($searchCriteria);

        return $products;
    }

    /**
     * Update product
     *
     * @param  mixed  $product
     * @return mixed
     */
    public function update($product)
    {
        $result = [];
        try {
            $productData = $this->jsonSerializer->serialize($product);
            $this->publisher->publish(self::TOPIC, $productData);
            $result['success'] = 'Product Id: '.$product['entity_id'].' updated successfully';
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }
}
