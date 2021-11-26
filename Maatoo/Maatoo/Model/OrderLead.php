<?php

namespace Maatoo\Maatoo\Model;

use Magento\Framework\Model\AbstractModel;

class OrderLead extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Maatoo\Maatoo\Model\ResourceModel\OrderLead::class);
    }
}
