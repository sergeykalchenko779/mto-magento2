<?php

namespace Maatoo\Maatoo\Model;

use Magento\Framework\Model\AbstractModel;
use Maatoo\Maatoo\Api\Data\SyncInterface;

/**
 * Class Store
 * @method \Maatoo\Maatoo\Model\Store getMaatooStoreId()
 * @method \Maatoo\Maatoo\Model\Store getStoreId()
 * @method \Maatoo\Maatoo\Model\Store setMaatooStoreId($maatooStoreId)
 * @method \Maatoo\Maatoo\Model\Store setStoreId($storeId)
 * @package Maatoo\Maatoo\Model
 */
class Store extends AbstractModel
{
    /**
     *
     */
    protected function _construct()
    {
        $this->_init(\Maatoo\Maatoo\Model\ResourceModel\Store::class);
    }
}
