<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Order;

/**
 * Class OrderProductModel
 * Represents a product inside an order product.
 */
class OrderProductModel
{
    /** @var array */
    protected array $data = [];

    /**
     * OrderProductModel constructor.
     * @param array|object $data Raw product data
     */
    public function __construct($data = [])
    {
        $arr = $this->convertToArray($data);

        if (isset($arr['WebSiparisUrun']) && is_array($arr['WebSiparisUrun'])) {
            $arr = $arr['WebSiparisUrun'];
        }

        $this->data = $arr;
    }

    /**
     * Recursively convert data to array
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
     * Magic getter
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if field exists
     * @param string $name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
