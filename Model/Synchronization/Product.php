<?php

namespace Maatoo\Maatoo\Model\Synchronization;

use Maatoo\Maatoo\Api\Data\SyncInterface;

/**
 * Class Product
 * @package Maatoo\Maatoo\Model\Synchronization
 */
class Product
{
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBilder;
    /**
     * @var \Magento\Catalog\Helper\Product
     */
    private $productHelper;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Maatoo\Maatoo\Model\StoreConfigManager
     */
    private $storeManager;
    /**
     * @var \Maatoo\Maatoo\Adapter\AdapterInterface
     */
    private $adapter;
    /**
     * @var \Maatoo\Maatoo\Model\StoreMap
     */
    private $storeMap;
    /**
     * @var \Maatoo\Maatoo\Model\SyncRepository
     */
    private $syncRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryCollectionFactory;

    private $syncCategory;
    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    private $configurableProductType;

    /**
     * Product constructor.
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\UrlInterface $urlBilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Maatoo\Maatoo\Model\StoreConfigManager $storeManager
     * @param \Maatoo\Maatoo\Adapter\AdapterInterface $adapter
     * @param \Maatoo\Maatoo\Model\StoreMap $storeMap
     * @param \Maatoo\Maatoo\Model\SyncRepository $syncRepository
     * @param Category $syncCategory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\UrlInterface $urlBilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Maatoo\Maatoo\Model\StoreConfigManager $storeManager,
        \Maatoo\Maatoo\Adapter\AdapterInterface $adapter,
        \Maatoo\Maatoo\Model\StoreMap $storeMap,
        \Maatoo\Maatoo\Model\SyncRepository $syncRepository,
        \Maatoo\Maatoo\Model\Synchronization\Category $syncCategory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType

    )
    {
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->collectionFactory = $collectionFactory;
        $this->productHelper = $productHelper;
        $this->adapter = $adapter;
        $this->storeMap = $storeMap;
        $this->syncRepository = $syncRepository;
        $this->urlBilder = $urlBilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->syncCategory = $syncCategory;
        $this->configurableProductType = $configurableProductType;
    }

    /**
     * @param \Closure|null $cl
     */
    public function sync(\Closure $cl = null)
    {
        $this->syncCategory->sync($cl);

        $parameters = [];
        $categoryMaatoo = $this->adapter->makeRequest('product-categories', $parameters, 'GET');
        if (empty($categoryMaatoo['total'])) {
            if (is_callable($cl)) {
                $cl('Before loading products you must load product categories');
            }
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $attributes = ['sku', 'product_url', 'name', 'store_id', 'image', 'price', 'visibility', 'description'];
            $collection = $this->collectionFactory->create();
            $collection->addAttributeToSelect($attributes)
                ->addStoreFilter($store);

            foreach ($collection as $product) {

                /** @var \Maatoo\Maatoo\Model\Sync $sync */
                $sync = $this->syncRepository->getByParam([
                    'entity_id' => $product->getId(),
                    'entity_type' => SyncInterface::TYPE_PRODUCT,
                    'store_id' => $store->getId(),
                ]);

                if ($sync->getData('status') == SyncInterface::STATUS_SYNCHRONIZED) {
                    continue;
                }


                $price = $this->productHelper->getPrice($product);
                $categoryIds = $product->getCategoryIds();

                // Don't allow empty categories
                if (empty($categoryIds)) {
                    continue;
                }

                $categoryId = 0;
                $level = 0;
                $categoryCollection = $this->categoryCollectionFactory->create();
                $categoryCollection->addFieldToFilter('entity_id', $categoryIds);
                /** @var \Magento\Catalog\Api\Data\CategoryInterface $_category */
                foreach ($categoryCollection as $_category) {
                    if ($_category->getLevel() > $level) {
                        $categoryId = $_category->getId();
                        $level = $_category->getLevel();
                    }
                }

                $maatooSyncCategoryRow = $this->syncRepository->getRow([
                    'entity_id' => $categoryId,
                    'entity_type' => SyncInterface::TYPE_CATEGORY,
                    'store_id' => $store->getId()]);

                if (!isset($maatooSyncCategoryRow['maatoo_id'])) {
                    continue;
                }

                $maatooCategoryId = $maatooSyncCategoryRow['maatoo_id'];

                $parameters = [
                    'store' => $this->storeMap->getStoreToMaatoo($store->getId()),
                    "category" => $categoryIds[0],
                    "externalProductId" => $product->getId(),
                    "price" => number_format($price, 2, '.', ''),
                    "url" => mb_substr($this->productHelper->getProductUrl($product), 0, 190),
                    "title" => mb_substr($product->getName(), 0, 190),
                    "description" => $product->getDescription() ?? '-',
                    "sku" => $product->getSku(),
                    "imageUrl" => $this->productHelper->getImageUrl($product),
                    "productCategory" => $maatooCategoryId
                ];

                $result = [];

                if (empty($sync->getData('status')) || $sync->getData('status') == SyncInterface::STATUS_EMPTY) {
                    $result = $this->adapter->makeRequest('products/new', $parameters, 'POST');
                    if (is_callable($cl)) {
                        $cl('Added product #' . $product->getId()) . ' ' . $product->getName();
                    }
                } elseif ($sync->getData('status') == SyncInterface::STATUS_UPDATED) {
                    $result = $this->adapter->makeRequest('products/' . $sync->getData('maatoo_id') . '/edit', $parameters, 'PATCH');
                    if (is_callable($cl)) {
                        $cl('Updated product #' . $product->getId()) . ' ' . $product->getName();
                    }
                }

                if (isset($result['product']['id'])) {
                    /*$param = [
                        'entity_id' => $product->getId(),
                        'entity_type' => SyncInterface::TYPE_PRODUCT,
                        'store_id' => $store->getId(),
                    ];
                    // @var \Maatoo\Maatoo\Model\Sync $sync
                    $sync = $this->syncRepository->getByParam($param);*/
                    $sync->setStatus(SyncInterface::STATUS_SYNCHRONIZED);
                    $sync->setMaatooId($result['product']['id']);
                    $sync->setEntityId($product->getId());
                    $sync->setStoreId($store->getId());
                    $sync->setEntityType(SyncInterface::TYPE_PRODUCT);
                    $this->syncRepository->save($sync);
                }
            }

            // Delete entity
            $this->searchCriteriaBuilder->addFilter('entity_type', SyncInterface::TYPE_PRODUCT);
            $this->searchCriteriaBuilder->addFilter('status', SyncInterface::STATUS_DELETED);
            $this->searchCriteriaBuilder->addFilter('store_id', $store->getId());
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $collectionForDelete = $this->syncRepository->getList($searchCriteria);
            foreach ($collectionForDelete as $item) {
                $result = $this->adapter->makeRequest('products/' . $item->getMaatooId() . '/delete', [], 'DELETE');
                if (is_callable($cl)) {
                    $cl('Deleted product #' . $item->getMaatooId());
                }

                $this->syncRepository->delete($item);
            }
        }
    }

}
