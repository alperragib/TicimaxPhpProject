<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model;

/**
 * Base model class for all models in the system
 */
abstract class BaseModel
{
    /** @var array */
    protected array $data = [];

    /**
     * BaseModel constructor.
     * @param array|object $data
     */
    public function __construct($data = [])
    {
        $this->data = $this->convertToArray($data);
    }

    /**
     * Convert data to array recursively.
     */
    protected function convertToArray($data)
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
     * Magic getter method.
     */
    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    /**
     * Magic setter method.
     */
    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if a property exists.
     * @param string $name Property name to check
     * @return bool True if the property exists, false otherwise
     */
    public function __isset(string $name): bool
    {
        return isset($this->data[$name]);
    }

    /**
     * Convert the model to an array.
     */
    public function toArray(): array
    {
        return $this->data;
    }
} 