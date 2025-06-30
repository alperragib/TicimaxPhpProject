<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Order;

use AlperRagib\Ticimax\Model\Order\DeliveryAddressModel;
use AlperRagib\Ticimax\Model\Order\OrderProductModel;

/**
 * Class OrderModel
 * Represents an order in the Ticimax system.
 */
class OrderModel
{
    /** @var array */
    protected array $data = [];

    /**
     * OrderModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        $arr = $this->convertToArray($data);

        if (isset($arr['TeslimatAdresi']) && is_array($arr['TeslimatAdresi'])) {
            $arr['TeslimatAdresi'] = new DeliveryAddressModel($arr['TeslimatAdresi']);
        }

        if (isset($arr['Urunler'])) {
            $urunler = $arr['Urunler'];
            if (is_array($urunler)) {
                if (isset($urunler[0])) {
                    $arr['Urunler'] = array_map(fn($u) => new OrderProductModel($u), $urunler);
                } else {
                    $arr['Urunler'] = [new OrderProductModel($urunler)];
                }
            }
        }

        $this->data = $arr;
    }

    /**
     * Convert data to array recursively.
     * @param mixed $data
     * @return mixed
     */
    private function convertToArray($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'convertToArray'], $data);
        }

        if (is_object($data)) {
            return $this->convertToArray((array) $data);
        }

        // Return primitive values as-is
        return $data;
    }

    /**
     * Magic getter method.
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter method.
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if a property exists.
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert the order to an array for API requests.
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->data;

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
