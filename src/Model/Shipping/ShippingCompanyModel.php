<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Shipping;

/**
 * Class ShippingCompanyModel
 * Represents a shipping company in the Ticimax system
 */
class ShippingCompanyModel
{
    /** @var array */
    protected array $data = [];

    /**
     * ShippingCompanyModel constructor.
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
     * Returns the data as-is, using original field names only.
     */
    public function toArray(): array
    {
        return $this->data;
    }

    // Helper methods for common operations
    
    public function getId(): int
    {
        return $this->ID ?? 0;
    }

    public function getName(): string
    {
        return $this->Tanim ?? '';
    }

    public function hasCashOnDelivery(): bool
    {
        return $this->KapidaOdeme ?? false;
    }

    public function getCashOnDeliveryPrice(): float
    {
        return $this->KapidaOdemeFiyati ?? 0.0;
    }

    public function hasCreditCardOnDelivery(): bool
    {
        return $this->KapidaOdemeKK ?? false;
    }

    public function getCreditCardOnDeliveryPrice(): float
    {
        return $this->KapidaOdemeKKFiyati ?? 0.0;
    }
} 