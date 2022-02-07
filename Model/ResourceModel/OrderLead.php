<?php

namespace Maatoo\Maatoo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class OrderLead extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('maatoo_order_lead', 'entity_id');
    }
}
