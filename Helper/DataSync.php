<?php

namespace Maatoo\Maatoo\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ResourceConnection;

/**
 * Class DataSync
 *
 * @package Maatoo\Maatoo\Helper
 */
class DataSync extends AbstractHelper
{

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resource;

    /**
     * @param  ResourceConnection  $resource
     * @param  Context  $context
     */
    public function __construct(
        ResourceConnection $resource,
        Context $context
    ) {
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * @param $updateData
     *
     * @return void
     */
    public function executeUpdateSalesOrderTable($updateData)
    {
        $tableName = $this->resource->getTableName('sales_order');
        $connection = $this->resource->getConnection();
        $conditions = [];

        foreach ($updateData as $entity_id => $maatoo_sync) {
            $case = $connection->quoteInto('?', $entity_id);
            $result = $connection->quoteInto('?', $maatoo_sync);
            $conditions[$case] = $result;
        }

        $value = $connection->getCaseSql('entity_id', $conditions, 'maatoo_sync');
        $where = ['entity_id IN (?)' => array_keys($updateData)];

        try {
            $connection->beginTransaction();
            $connection->update($tableName, ['maatoo_sync' => $value], $where);
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * @param $data
     *
     * @return void
     */
    public function executeInsertOnDuplicate($data)
    {
        $tableName = $this->resource->getTableName('maatoo_sync');
        $connection = $this->resource->getConnection();
        $connection->insertOnDuplicate($tableName, $data);
    }

    /**
     * @param $maatoSyncInsertData
     * @param $resultOrderLines
     *
     * @return array
     */
    public function setMaatooIdToInsertArray($maatoSyncInsertData, $resultOrderLines)
    {
        foreach ($resultOrderLines as $resultKey => $resultOrderLine) {
            $maatoSyncInsertData[$resultKey]['maatoo_id'] = $resultOrderLine['id'];
        }

        return $maatoSyncInsertData;
    }
}
