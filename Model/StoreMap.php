<?php

namespace Maatoo\Maatoo\Model;

class StoreMap
{
    private $maatooStoreRepository;

    private $maatooToStore = [];

    private $storeToMaatoo = [];

    public function __construct(
        \Maatoo\Maatoo\Model\StoreRepository $maatooStoreRepository
    )
    {
        $this->maatooStoreRepository = $maatooStoreRepository;
    }

    public function getMaatooToStore($maatooStoreId)
    {
        if(empty($this->maatooToStore)) {
            $this->loadMap();
        }

        if(!isset($this->maatooToStore['id_'.$maatooStoreId])) {
            return '';
        }

        return $this->maatooToStore['id_'.$maatooStoreId];
    }

    public function getStoreToMaatoo($storeId)
    {
        if(empty($this->storeToMaatoo)) {
            $this->loadMap();
        }

        if(!isset($this->storeToMaatoo['id_'.$storeId])) {
            return '';
        }

        return $this->storeToMaatoo['id_'.$storeId];
    }

    private function loadMap()
    {
        /** @var \Maatoo\Maatoo\Model\Store $store */
        foreach ($this->maatooStoreRepository->getList() as $store)
        {
            $this->maatooToStore = array_merge($this->maatooToStore,['id_'.$store->getMaatooStoreId() => $store->getStoreId()]);
            $this->storeToMaatoo = array_merge($this->storeToMaatoo,['id_'.$store->getStoreId() => $store->getMaatooStoreId()]);
        }
    }
}
