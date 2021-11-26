<?php

namespace Maatoo\Maatoo\Api\Data;

/**
 * Interface OrderSync
 * @package Maatoo\Maatoo\Api\Data
 */
interface SyncInterface
{

    /**
     * entity type is customer
     */
    const TYPE_CUSTOMER = 1;

    /**
     * entity type is order
     */
    const TYPE_ORDER = 2;

    /**
     * entity type is category
     */
    const TYPE_CATEGORY = 3;

    /**
     * entity type is product
     */
    const TYPE_PRODUCT = 4;

    /**
     * entity type is order_lines
     */
    const TYPE_ORDER_LINES = 5;

    /**
     * the entity is not in the sync table
     */
    const STATUS_EMPTY = 0;

    /**
     * the entity is synchronized
     */
    const STATUS_SYNCHRONIZED = 1;

    /**
     * the entity is updated, need to sync
     */
    const STATUS_UPDATED = 2;

    /**
     * the entity is deleted, need to sync
     */
    const STATUS_DELETED = 3;

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     * @return mixed
     */
    public function setEntityId(int $entityId);

    /**
     * @return int
     */
    public function getEntityType();

    /**
     * @param int $entityType
     * @return mixed
     */
    public function setEntityType(int $entityType);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     * @return mixed
     */
    public function setStatus(int $status);
}
