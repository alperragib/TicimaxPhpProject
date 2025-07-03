<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Order;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\Order\DeliveryAddressModel;
use AlperRagib\Ticimax\Model\Order\OrderProductModel;

/**
 * Class OrderModel
 * Represents an order in the Ticimax system.
 */
class OrderModel extends BaseModel
{
    /**
     * OrderModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        parent::__construct($data);

        // Handle delivery address
        if (isset($this->data['TeslimatAdresi']) && is_array($this->data['TeslimatAdresi'])) {
            $this->data['TeslimatAdresi'] = new DeliveryAddressModel($this->data['TeslimatAdresi']);
        }

        // Handle products
        if (isset($this->data['Urunler'])) {
            $urunler = $this->data['Urunler'];
            if (is_array($urunler)) {
                if (isset($urunler[0])) {
                    $this->data['Urunler'] = array_map(fn($u) => new OrderProductModel($u), $urunler);
                } else {
                    $this->data['Urunler'] = [new OrderProductModel($urunler)];
                }
            }
        }
    }

    /**
     * Convert the order to an array for API requests.
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        if (isset($data['TeslimatAdresi']) && $data['TeslimatAdresi'] instanceof DeliveryAddressModel) {
            $data['TeslimatAdresi'] = $data['TeslimatAdresi']->toArray();
        }

        if (isset($data['Urunler']) && is_array($data['Urunler'])) {
            $data['Urunler'] = array_map(function ($urun) {
                return $urun instanceof OrderProductModel ? $urun->toArray() : $urun;
            }, $data['Urunler']);
        }

        return $data;
    }
}
