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
        // Önce veriyi array'e çevirelim
        $arr = $this->convertToArray($data);

        // Özel veri dönüşümü - API'den gelen veri WebSiparisUrun içinde olabilir
        if (isset($arr['WebSiparisUrun']) && is_array($arr['WebSiparisUrun'])) {
            $arr = $arr['WebSiparisUrun'];
        }

        // Parent constructor'ı çağıralım
        parent::__construct($arr);
    }
}
