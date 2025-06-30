<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Product;

/**
 * Class ProductVariationModel
 * Represents a product variation in the Ticimax system.
 */
class ProductVariationModel
{
    /** @var array */
    protected array $data = [];

    /**
     * ProductVariationModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        $this->data = $this->convertToArray($data);
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
     * Convert the product variation to an array for API requests.
     * @return array|false
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
