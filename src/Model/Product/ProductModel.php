<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Product;

use AlperRagib\Ticimax\Model\Product\ProductVariationModel;

/**
 * Class ProductModel
 * Represents a product in the Ticimax system.
 */
class ProductModel
{
    /** @var array */
    protected array $data = [];

    /**
     * ProductModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        $arr = $this->convertToArray($data);

        if (isset($arr['Varyasyonlar']['Varyasyon'])) {
            $varyasyonlar = $arr['Varyasyonlar']['Varyasyon'];
            if (is_array($varyasyonlar) && array_keys($varyasyonlar) === range(0, count($varyasyonlar) - 1)) {
                $arr['Varyasyonlar'] = array_map(function ($v) {
                    return new ProductVariationModel($v);
                }, $varyasyonlar);
            } else {
                $arr['Varyasyonlar'] = [new ProductVariationModel($varyasyonlar)];
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
     * Add a variation to the product.
     * @param ProductVariationModel $variation
     */
    public function addVariation(ProductVariationModel $variation): void
    {
        if (!isset($this->data['Varyasyonlar'])) {
            $this->data['Varyasyonlar'] = [];
        }
        $this->data['Varyasyonlar'][] = $variation;
    }

    /**
     * Returns the data as-is, using original field names only.
     */
    public function toArray(): array
    {
        $data = $this->data;

        // Convert variation objects to arrays if they exist
        if (isset($data['Varyasyonlar']) && is_array($data['Varyasyonlar'])) {
            $data['Varyasyonlar'] = array_map(function ($variation) {
                return $variation instanceof ProductVariationModel ? $variation->toArray() : $variation;
            }, $data['Varyasyonlar']);
        }

        return $data;
    }
}
