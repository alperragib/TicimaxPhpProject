<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Product;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\Product\ProductVariationModel;

/**
 * Class ProductModel
 * Represents a product in the Ticimax system.
 */
class ProductModel extends BaseModel
{
    /**
     * ProductModel constructor.
     * @param array|object $data (should use original API/source field names)
     */
    public function __construct($data = [])
    {
        parent::__construct($data);

        // Handle variations
        if (isset($this->data['Varyasyonlar']['Varyasyon'])) {
            $varyasyonlar = $this->data['Varyasyonlar']['Varyasyon'];
            if (is_array($varyasyonlar) && array_keys($varyasyonlar) === range(0, count($varyasyonlar) - 1)) {
                $this->data['Varyasyonlar'] = array_map(function ($v) {
                    return new ProductVariationModel($v);
                }, $varyasyonlar);
            } else {
                $this->data['Varyasyonlar'] = [new ProductVariationModel($varyasyonlar)];
            }
        }
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
        $data = parent::toArray();

        // Convert variation objects to arrays if they exist
        if (isset($data['Varyasyonlar']) && is_array($data['Varyasyonlar'])) {
            $data['Varyasyonlar'] = array_map(function ($variation) {
                return $variation instanceof ProductVariationModel ? $variation->toArray() : $variation;
            }, $data['Varyasyonlar']);
        }

        return $data;
    }
}
