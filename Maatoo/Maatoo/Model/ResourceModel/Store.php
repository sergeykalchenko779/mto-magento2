<?php

namespace Maatoo\Maatoo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Store extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('maatoo_store', 'id');
    }

    public function loadByStoresId(AbstractModel $object, int $maatooStoreId, int $storeId)
    {
        $tableName = $this->getTable('maatoo_store');
        $select = $this->getConnection()
            ->select()
            ->from($tableName)
            ->where('maatoo_store_id=?', $maatooStoreId)
            ->where('store_id=?', $storeId);

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
}
