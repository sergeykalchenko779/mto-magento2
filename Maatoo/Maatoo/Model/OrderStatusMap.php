<?php

namespace Maatoo\Maatoo\Model;

class OrderStatusMap
{
    const DRAFT = 'draft';

    const ORDER_STATUS_MAP = [
        'processing' => 'open',
        'pending_payment' => 'open',
        'payment_review' => 'open',
        'fraud' => 'failed',
        'pending' => 'open',
        'holded' => 'incomplete',
        'complete' => 'complete',
        'closed' => 'complete',
        'canceled' => 'canceled'
    ];

    public static function getStatus($status)
    {
        if(isset(self::ORDER_STATUS_MAP[$status])) {
            return self::ORDER_STATUS_MAP[$status];
        } else {
            return self::ORDER_STATUS_MAP['holded'];
        }
    }
}
