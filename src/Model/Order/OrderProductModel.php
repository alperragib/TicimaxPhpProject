<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Order;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class OrderProductModel
 * Represents an order product in the Ticimax system.
 * 
 * Not: API'den gelen veri iki formatta olabilir:
 * 1. { "WebSiparisUrun": { "UrunID": 123, ... } }
 * 2. { "UrunID": 123, ... }
 */
class OrderProductModel extends BaseModel
{
    /**
     * OrderProductModel constructor.
     * @param array|object $data Raw product data
     */
    public function __construct($data = [])
    {
        // First, let's convert the data to array
        $arr = $this->convertToArray($data);

        // Special data transformation - API data may come within WebSiparisUrun
        if (isset($arr['WebSiparisUrun']) && is_array($arr['WebSiparisUrun'])) {
            $arr = $arr['WebSiparisUrun'];
        }

        // Call parent constructor
        parent::__construct($arr);
    }
}
