<?php

namespace Maatoo\Maatoo\Model;


class SyncRepository
{
    private $syncFactory;

    private $syncResource;

    private $collectionFactory;

    private $collectionProcessor;

    public function __construct(
        \Maatoo\Maatoo\Model\SyncFactory $syncFactory,
        \Maatoo\Maatoo\Model\ResourceModel\Sync $syncResource,
        \Maatoo\Maatoo\Model\ResourceModel\Sync\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    )
    {
        $this->syncFactory = $syncFactory;
        $this->syncResource = $syncResource;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function getByParam(array $param)
    {
        $sync = $this->syncFactory->create();
        $this->syncResource->loadByParam($sync, $param);
        return $sync;
    }

    public function getRow(array $param)
    {
        return $this->syncResource->getRow($param);
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria,$collection);
        return $collection;

    }

    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        return $this->syncResource->save($object);
    }

    public function delete(\Magento\Framework\Model\AbstractModel $object)
    {
        $this->syncResource->delete($object);
    }

}
