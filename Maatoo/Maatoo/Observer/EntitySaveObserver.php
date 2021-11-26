<?php

namespace Maatoo\Maatoo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Maatoo\Maatoo\Api\Data\SyncInterface;

/**
 * Class EntitySaveObserver
 * @package Maatoo\Maatoo\Observer
 */
class EntitySaveObserver implements ObserverInterface
{

    /**
     * @var \Maatoo\Maatoo\Model\StoreConfigManager
     */
    private $storeManager;

    /**
     * @var \Maatoo\Maatoo\Model\SyncFactory
     */
    private $syncFactory;

    /**
     * @var \Maatoo\Maatoo\Model\ResourceModel\Sync
     */
    private $syncResource;

    /**
     * @var
     */
    private $entityType;

    /**
     * EntitySaveObserver constructor.
     * @param \Maatoo\Maatoo\Model\SyncFactory $syncFactory
     * @param \Maatoo\Maatoo\Model\ResourceModel\Sync $syncResource
     * @param $entityType
     */
    public function __construct(
        \Maatoo\Maatoo\Model\SyncFactory $syncFactory,
        \Maatoo\Maatoo\Model\ResourceModel\Sync $syncResource,
        \Maatoo\Maatoo\Model\StoreConfigManager $storeManager,
        $entityType
    )
    {
        $this->syncFactory = $syncFactory;
        $this->syncResource = $syncResource;
        $this->storeManager = $storeManager;
        $this->entityType = $entityType;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $entity = $observer->getEvent()->getData('data_object');
        /*$extensionAttribute = $entity->getExtensionAttributes();
        if (!$extensionAttribute && !$extensionAttribute->getMaatooStatus) {
            return;
        }*/

        $entityId = (int)$entity->getId();

        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($this->storeManager->getStores() as $store)
        {
            $statusSync = SyncInterface::STATUS_EMPTY;

            /** @var \Maatoo\Maatoo\Model\Sync $sync */
            $sync = $this->syncFactory->create();
            $this->syncResource->loadByType($sync, $entityId, $this->entityType, $store->getId());

            if ($sync->getSyncId()) {
                if ($sync->getStatus() == SyncInterface::STATUS_SYNCHRONIZED) {
                    $statusSync = SyncInterface::STATUS_UPDATED;
                } else {
                    $statusSync = $sync->getStatus();
                }
            }

            $sync->setEntityId($entityId);
            $sync->setEntityType($this->entityType);
            $sync->setStoreId($store->getId());
            $sync->setStatus($statusSync);
            $this->syncResource->save($sync);
        }
    }
}
