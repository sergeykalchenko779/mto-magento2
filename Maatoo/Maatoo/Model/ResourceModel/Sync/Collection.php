<?php

namespace Maatoo\Maatoo\Model\ResourceModel\Sync;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            \Maatoo\Maatoo\Model\Sync::class,
            \Maatoo\Maatoo\Model\ResourceModel\Sync::class
        );
    }
}
