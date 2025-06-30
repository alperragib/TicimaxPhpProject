<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Order;

/**
 * Class DeliveryAddressModel
 * Represents the delivery address (TeslimatAdresi) in the Ticimax system.
 */
class DeliveryAddressModel
{
    /** @var array */
    protected array $data = [];

    /**
     * DeliveryAddressModel constructor.
     * @param array|object $data Raw delivery address data
     */
    public function __construct($data = [])
    {
        $this->data = $this->convertToArray($data);
    }

    /**
     * Recursively convert any object or nested array to array
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

        return $data;
    }

    /**
     * Magic getter.
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter.
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if a field exists.
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert this model to array (e.g., for serialization).
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}