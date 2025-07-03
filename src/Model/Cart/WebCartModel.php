<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Cart;

use AlperRagib\Ticimax\Model\BaseModel;
use AlperRagib\Ticimax\Model\Cart\WebCartProductModel;

/**
 * Class WebCartModel
 * Represents a web cart in the Ticimax system.
 */
class WebCartModel extends BaseModel
{
   
    /**
     * Get cart products
     * @return WebCartProductModel[]
     */
    public function getProducts(): array
    {
        $products = $this->data['Urunler'] ?? [];
        if (empty($products)) {
            return [];
        }

        return array_map(function ($productData) {
            return new WebCartProductModel($productData);
        }, $products);
    }

    /**
     * Add a product to cart
     * @param WebCartProductModel $product
     */
    public function addProduct(WebCartProductModel $product): void
    {
        if (!isset($this->data['Urunler'])) {
            $this->data['Urunler'] = [];
        }
        $this->data['Urunler'][] = $product->toArray();
    }

    /**
     * Get total number of products in cart
     * @return int
     */
    public function getProductCount(): int
    {
        return count($this->getProducts());
    }
} 