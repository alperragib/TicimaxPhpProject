<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Cart;

/**
 * Class WebCartProductModel
 * Represents a web cart product in the Ticimax system.
 */
class WebCartProductModel
{
    /** @var array */
    protected array $data = [];

    /**
     * WebCartProductModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        $arr = $this->convertToArray($data);
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
     * Returns the data as-is, using original field names only.
     */
    public function toArray(): array
    {
        $data = $this->data;
        return $data;
    }
} 