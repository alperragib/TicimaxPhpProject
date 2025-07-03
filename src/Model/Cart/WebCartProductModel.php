<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Cart;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class WebCartProductModel
 * Represents a web cart product in the Ticimax system.
 */
class WebCartProductModel extends BaseModel
{

    /**
     * Get product quantity
     * @return int
     */
    public function getQuantity(): int
    {
        return (int)($this->data['Adet'] ?? 0);
    }

    /**
     * Set product quantity
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->data['Adet'] = $quantity;
    }

    /**
     * Get product price
     * @return float
     */
    public function getPrice(): float
    {
        return (float)($this->data['Fiyat'] ?? 0.0);
    }

    /**
     * Get product ID
     * @return int|null
     */
    public function getProductId(): ?int
    {
        return isset($this->data['UrunID']) ? (int)$this->data['UrunID'] : null;
    }

    /**
     * Get product name
     * @return string|null
     */
    public function getProductName(): ?string
    {
        return $this->data['UrunAdi'] ?? null;
    }
} 