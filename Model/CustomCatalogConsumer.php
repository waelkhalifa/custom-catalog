<?php


namespace Wael\CustomCatalog\Model;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\MessageQueue\MessageLockException;
use Magento\Framework\MessageQueue\ConnectionLostException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\MessageQueue\CallbackInvoker;
use Magento\Framework\MessageQueue\ConsumerConfigurationInterface;
use Magento\Framework\MessageQueue\EnvelopeInterface;
use Magento\Framework\MessageQueue\QueueInterface;
use Magento\Framework\MessageQueue\LockInterface;
use Magento\Framework\MessageQueue\MessageController;
use Magento\Framework\MessageQueue\ConsumerInterface;

/**
 * Class CustomCatalogConsumer
 * @package Wael\CustomCatalog\Model
 */
class CustomCatalogConsumer implements ConsumerInterface
{
    /**
     * @var \Magento\Framework\MessageQueue\CallbackInvoker
     */
    private $invoker;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;
    /**
     * @var \Magento\Framework\MessageQueue\ConsumerConfigurationInterface
     */
    private $configuration;
    /**
     * @var \Magento\Framework\MessageQueue\MessageController
     */
    private $messageController;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var  \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonSerializer;

    /**
     * CustomCatalogConsumer constructor.
     * @param  CallbackInvoker  $invoker
     * @param  ResourceConnection  $resource
     * @param  MessageController  $messageController
     * @param  ConsumerConfigurationInterface  $configuration
     * @param  \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository
     * @param  \Magento\Framework\Serialize\Serializer\Json  $jsonSerializer
     */
    public function __construct(
        CallbackInvoker $invoker,
        ResourceConnection $resource,
        MessageController $messageController,
        ConsumerConfigurationInterface $configuration,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->invoker = $invoker;
        $this->resource = $resource;
        $this->messageController = $messageController;
        $this->configuration = $configuration;
        $this->productRepository = $productRepository;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @inheritdoc
     */
    public function process($maxNumberOfMessages = null)
    {
        $queue = $this->configuration->getQueue();
        if ( ! isset($maxNumberOfMessages)) {
            $queue->subscribe($this->getTransactionCallback($queue));
        } else {
            $this->invoker->invoke($queue, $maxNumberOfMessages, $this->getTransactionCallback($queue));
        }
    }

    /**
     * Get transaction callback. This handles the case of both sync and async.
     *
     * @param  QueueInterface  $queue
     * @return \Closure
     */
    private function getTransactionCallback(QueueInterface $queue)
    {
        return function (EnvelopeInterface $message) use ($queue) {
            /** @var LockInterface $lock */
            $lock = null;
            try {
                $lock = $this->messageController->lock($message, $this->configuration->getConsumerName());
                $product = $this->updateProduct($message);
                if ($product === false) {
                    $queue->reject($message);
                } else {
                    // For me to get to know if there is an error.
                    print_r('Product Id : '.$product->getId().' has updated successfully.'.PHP_EOL);
                }
                $queue->acknowledge($message);
            } catch (MessageLockException $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
            } catch (ConnectionLostException $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
                if ($lock) {
                    $this->resource->getConnection()
                                   ->delete($this->resource->getTableName('queue_lock'), ['id = ?' => $lock->getId()]);
                }
            } catch (NotFoundException $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
            } catch (\Exception $e) {
                $queue->reject($message, false, $e->getMessage());
                $queue->acknowledge($message);
                if ($lock) {
                    $this->resource->getConnection()
                                   ->delete($this->resource->getTableName('queue_lock'), ['id = ?' => $lock->getId()]);
                }
            }
        };
    }

    /**
     * @param $message
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateProduct($message)
    {
        $messageBody = $this->jsonSerializer->unserialize($this->jsonSerializer
            ->unserialize($message->getBody()));
        $product = $this->productRepository->getById($messageBody['entity_id']);
        $product->setData('copy_write_info', $messageBody['copy_write_info']);
        $product->setData('vpn', $messageBody['vpn']);
        $product->setStoreId(0);

        return $this->productRepository->save($product);
    }
}
