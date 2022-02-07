<?php

namespace Maatoo\Maatoo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderSync
 * @package Maatoo\Maatoo\Model\ResourceModel
 */
class Sync extends AbstractDb
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init('maatoo_sync', 'sync_id');
    }

    public function loadByType(AbstractModel $object, int $entityId, int $entityType, $storeId = null)
    {

        $tableName = $this->getTable('maatoo_sync');
        $select = $this->getConnection()
            ->select()
            ->from($tableName)
            ->where('entity_id=?', $entityId)
            ->where('entity_type=?', $entityType);

        if($storeId!=null) {
            $select->where('store_id=?', $storeId);
        }

        $data = $this->getConnection()
            ->fetchRow($select);

        if ($data) {
            $object->setData($data);
            $object->setOrigData();
            $this->_afterLoad($object);
            $object->afterLoad();
            return true;
        }
        return false;
    }

    public function loadByParam(AbstractModel $object, array $param)
    {
        $tableName = $this->getTable('maatoo_sync');
        $select = $this->getConnection()
            ->select()
            ->from($tableName);

        foreach ($param as $key => $value)
        {
            $select->where($key.'=?', $value);
        }

        $data = $this->getConnection()
            ->fetchRow($select);

        if ($data) {
            $object->setData($data);
            $object->setOrigData();
            $this->_afterLoad($object);
            $object->afterLoad();
            return true;
        }
        return false;
    }

    public function getRow($param)
    {
        $tableName = $this->getTable('maatoo_sync');
        $select = $this->getConnection()
            ->select()
            ->from($tableName);

        foreach ($param as $key => $value)
        {
            $select->where($key.'=?', $value);
        }

        $data = $this->getConnection()
            ->fetchRow($select);

        return $data;
    }
}
