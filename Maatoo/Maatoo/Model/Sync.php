<?php

namespace Maatoo\Maatoo\Model;

use Magento\Framework\Model\AbstractModel;
use Maatoo\Maatoo\Api\Data\SyncInterface;

/**
 * Class Sync
 * @package Maatoo\Maatoo\Model
 */
class Sync extends AbstractModel implements SyncInterface
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(\Maatoo\Maatoo\Model\ResourceModel\Sync::class);
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->getData('entity_type');
    }

    /**
     * @param int $entityType
     * @return mixed
     */
    public function setEntityType(int $entityType)
    {
        return $this->setData('entity_type', $entityType);
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->getData('status');
    }

    /**
     * @param int $status
     * @return this
     */
    public function setStatus(int $status)
    {
        return $this->setData('status', $status);
    }

    /**
     * @return mixed
     */
    public function getMaatooId()
    {
        return $this->getData('maatoo_id');
    }

    /**
     * @param int $maatooId
     * @return this
     */
    public function setMaatooId(int $maatooId)
    {
        return $this->setData('maatoo_id', $maatooId);
    }

}
