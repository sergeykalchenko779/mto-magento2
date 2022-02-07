<?php

namespace Maatoo\Maatoo\Model\ResourceModel\Store;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Maatoo\Maatoo\Model\Store::class,
            \Maatoo\Maatoo\Model\ResourceModel\Store::class
        );
    }
}
