<?php

namespace Maatoo\Maatoo\Model\Synchronization;

/**
 * Class Store
 * @package Maatoo\Maatoo\Model\Synchronization
 */
class Store
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
     * @var \Maatoo\Maatoo\Model\StoreRepository
     */
    private $maatooStoreRepository;

    /**
     * Store constructor.
     * @param \Maatoo\Maatoo\Model\StoreConfigManager $storeManager
     * @param \Maatoo\Maatoo\Adapter\AdapterInterface $adapter
     * @param \Maatoo\Maatoo\Model\StoreRepository $maatooStoreRepository
     */
    public function __construct(
        \Maatoo\Maatoo\Model\StoreConfigManager $storeManager,
        \Maatoo\Maatoo\Adapter\AdapterInterface $adapter,
        \Maatoo\Maatoo\Model\StoreRepository $maatooStoreRepository
    )
    {
        $this->storeManager = $storeManager;
        $this->adapter = $adapter;
        $this->maatooStoreRepository = $maatooStoreRepository;

    }

    /**
     * @param \Closure|null $cl
     */
    public function sync(\Closure $cl = null)
    {
        $parameters = [];
        $storesMaatoo = $this->adapter->makeRequest('stores', $parameters, 'GET');

        foreach ($this->storeManager->getStores() as $store) {
            $addNew = true;
            $storesMaatooId = 0;
            foreach ($storesMaatoo['stores'] as $storeMaatoo) {
                if ($storeMaatoo['externalStoreId'] == $store->getId()) {
                    $addNew = false;
                    $storesMaatooId = $storeMaatoo['id'];
                }
            }

            $parameters = [
                'domain' => $store->getBaseUrl(),
                'name' => $store->getName(),
                'shortName' => $this->storeManager->getStoreShortName($store),
                'currency' => $store->getBaseCurrencyCode(),
                'externalStoreId' => $store->getId(),
                'platform' => 'magento',
            ];

            $result = [];
            if ($addNew) {
                $result = $this->adapter->makeRequest('stores/new', $parameters, 'POST');
                if(is_callable($cl)) {
                    $cl('Added store #' . $store->getId()) . ' ' . $store->getName();
                }
            } else {
                $result = $this->adapter->makeRequest('stores/' . $storesMaatooId . '/edit', $parameters, 'PATCH');
                if(is_callable($cl)) {
                    $cl('Updated store #' . $store->getId()) . ' ' . $store->getName();
                }
            }

            $maatooStoreModel = $this->maatooStoreRepository->getByStoresId($result['store']['id'], $store->getId());
            $maatooStoreModel->setMaatooStoreId($result['store']['id']);
            $maatooStoreModel->setStoreId($store->getId());
            $this->maatooStoreRepository->save($maatooStoreModel);
        }
    }
}
