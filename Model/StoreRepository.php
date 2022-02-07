<?php

namespace Maatoo\Maatoo\Model;


class StoreRepository
{

    private $maatooStoreFactory;

    private $maatooStoreResource;

    private $maatooStoreCollectionFactory;

    public function __construct(
        \Maatoo\Maatoo\Model\StoreFactory $maatooStoreFactory,
        \Maatoo\Maatoo\Model\ResourceModel\Store $maatooStoreResource,
        \Maatoo\Maatoo\Model\ResourceModel\Store\CollectionFactory $maatooStoreCollectionFactory
    )
    {
        $this->maatooStoreFactory = $maatooStoreFactory;
        $this->maatooStoreResource = $maatooStoreResource;
        $this->maatooStoreCollectionFactory = $maatooStoreCollectionFactory;
    }

    public function getByStoresId($maatooStoreId = null, $storeId = null)
    {
        $store = $this->maatooStoreFactory->create();
        if ($maatooStoreId != null && $storeId != null) {
            $this->maatooStoreResource->loadByStoresId($store, $maatooStoreId, $storeId);
        }
        return $store;
    }

    public function getList()
    {
        $collection = $this->maatooStoreCollectionFactory->create();
        return $collection->getItems();
    }

    public function save(\Magento\Framework\Model\AbstractModel $store)
    {
        return $this->maatooStoreResource->save($store);
    }
}
