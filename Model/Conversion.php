<?php

namespace Maatoo\Maatoo\Model;

use Magento\Framework\Model\AbstractModel;

class Conversion extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Maatoo\Maatoo\Model\ResourceModel\Conversion::class);
    }
}
