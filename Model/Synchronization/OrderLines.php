<?php

namespace Maatoo\Maatoo\Model\Synchronization;

use Maatoo\Maatoo\Api\Data\SyncInterface;
use Magento\Quote\Model\ResourceModel\Quote;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;

class OrderLines
{
    /**
     * @var \Maatoo\Maatoo\Model\StoreConfigManager
     */
    private $storeManager;

    /**
     * @var \Maatoo\Maatoo\Adapter\AdapterInterface
     */
    private $adapter;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $collectionOrderFactory;

    /**
     * @var Quote\Item\CollectionFactory
     */
    private $collectionQuoteItemFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Maatoo\Maatoo\Model\StoreMap
     */
    private $storeMap;

    /**
     * @var \Maatoo\Maatoo\Model\SyncRepository
     */
    private $syncRepository;

    /**
     * @var Order
     */
    private $syncOrder;

    /**
     * @var \Maatoo\Maatoo\Model\Config\Config
     */
    private $config;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Maatoo\Maatoo\Model\StoreConfigManager $storeManager
     * @param CollectionFactory $collectionOrderFactory
     * @param Quote\Item\CollectionFactory $collectionQuoteItemFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Maatoo\Maatoo\Adapter\AdapterInterface $adapter
     * @param \Maatoo\Maatoo\Model\StoreMap $storeMap
     * @param \Maatoo\Maatoo\Model\SyncRepository $syncRepository
     * @param Order $syncOrder
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Maatoo\Maatoo\Model\StoreConfigManager $storeManager,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $collectionOrderFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $collectionQuoteItemFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Maatoo\Maatoo\Adapter\AdapterInterface $adapter,
        \Maatoo\Maatoo\Model\StoreMap $storeMap,
        \Maatoo\Maatoo\Model\SyncRepository $syncRepository,
        \Maatoo\Maatoo\Model\Synchronization\Order $syncOrder,
        \Maatoo\Maatoo\Model\Config\Config $config,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->storeManager = $storeManager;
        $this->collectionOrderFactory = $collectionOrderFactory;
        $this->collectionQuoteItemFactory = $collectionQuoteItemFactory;
        $this->adapter = $adapter;
        $this->storeMap = $storeMap;
        $this->syncRepository = $syncRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->syncOrder = $syncOrder;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param \Closure|null $cl
     */
    public function sync(\Closure $cl = null)
    {
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($this->storeManager->getStores() as $store) {

            /** @var \Magento\Quote\Model\ResourceModel\Quote\Item\Collection $collection */
            $collection = $this->collectionQuoteItemFactory->create();
            $lifetime = $this->config->getOrderLifetime();

            $select = $collection->getSelect();
            $select->where(
                new \Zend_Db_Expr('TIME_TO_SEC(TIMEDIFF(CURRENT_TIMESTAMP, `updated_at`)) <= ' . $lifetime * 24 * 60 * 60)
            );

            // Don't sync quote items with inactive products
            $select->join([
                'additional_table' => $collection->getTable('catalog_product_entity_int')
            ],
                'main_table.product_id = additional_table.entity_id AND additional_table.attribute_id = 97 AND additional_table.value <> 2'
            );

            foreach ($collection->getItems() as $item) {

                /** @var \Maatoo\Maatoo\Model\Sync $sync */
                $sync = $this->syncRepository->getByParam([
                    'entity_id' => $item->getId(),
                    'entity_type' => SyncInterface::TYPE_ORDER_LINES,
                    'store_id' => $store->getId(),
                ]);

                if ($sync->getData('status') == SyncInterface::STATUS_SYNCHRONIZED) {
                    continue;
                }

                $maatooSyncProductRow = $this->syncRepository->getRow([
                    'entity_id' => $item->getData('product_id'),
                    'entity_type' => SyncInterface::TYPE_PRODUCT,
                    'store_id' => $item->getData('store_id')
                ]);

                if (empty($maatooSyncProductRow['maatoo_id'])) {
                    continue;
                }

                $maatooSyncOrderRow = $this->syncRepository->getRow([
                    'entity_id' => $item->getData('quote_id'),
                    'entity_type' => SyncInterface::TYPE_ORDER,
                    'store_id' => $item->getData('store_id')
                ]);

                if (empty($maatooSyncOrderRow['maatoo_id'])) {
                    continue;
                }

                $parameters = [
                    'store' => $this->storeMap->getStoreToMaatoo($store->getId()),
                    "product" => $maatooSyncProductRow['maatoo_id'],
                    "order" => $maatooSyncOrderRow['maatoo_id'],
                    "quantity" => $item->getData('qty')
                ];

                $result = [];

                if (empty($sync->getData('status')) || $sync->getData('status') == SyncInterface::STATUS_EMPTY) {
                    $result = $this->adapter->makeRequest('orderLines/new', $parameters, 'POST');
                    if (is_callable($cl)) {
                        $cl('Added item to order #' . $item->getItemId());
                    }
                } elseif ($sync->getData('status') == SyncInterface::STATUS_UPDATED) {
                    $result = $this->adapter->makeRequest('orderLines/' . $sync->getData('maatoo_id') . '/edit', $parameters, 'PATCH');
                    if (is_callable($cl)) {
                        $cl('Updated item in order #' . $item->getItemId());
                    }
                }

                if (isset($result['orderLine']['id'])) {
                    /*$param = [
                        'entity_id' => $item->getId(),
                        'entity_type' => SyncInterface::TYPE_ORDER_LINES,
                        'store_id' => $item->getStoreId(),
                    ];*/
                    // @var \Maatoo\Maatoo\Model\Sync $sync
                    //$sync = $this->syncRepository->getByParam($param);
                    $sync->setStatus(SyncInterface::STATUS_SYNCHRONIZED);
                    $sync->setMaatooId($result['orderLine']['id']);
                    $sync->setEntityId($item->getId());
                    $sync->setStoreId($item->getData('store_id'));
                    $sync->setEntityType(SyncInterface::TYPE_ORDER_LINES);
                    $this->syncRepository->save($sync);
                }
            }

            // Delete entity
            foreach ($this->storeManager->getStores() as $store) {
                $this->searchCriteriaBuilder->addFilter('entity_type', SyncInterface::TYPE_ORDER_LINES);
                $this->searchCriteriaBuilder->addFilter('status', SyncInterface::STATUS_DELETED);
                $this->searchCriteriaBuilder->addFilter('store_id', $store->getId());
                $searchCriteria = $this->searchCriteriaBuilder->create();
                $collectionForDelete = $this->syncRepository->getList($searchCriteria);
                foreach ($collectionForDelete as $itemDel) {
                    $result = $this->adapter->makeRequest('orderLines/' . $itemDel->getMaatooId() . '/delete', [], 'DELETE');
                    if (is_callable($cl)) {
                        $cl('Deleted item from order #' . $itemDel->getMaatooId());
                    }

                    $this->syncRepository->delete($itemDel);
                }
            }
        }
    }
}
