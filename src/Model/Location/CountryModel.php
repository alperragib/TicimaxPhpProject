<?php

namespace AlperRagib\Ticimax\Model\Location;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class CountryModel
 * Represents a country in the Ticimax system.
 */
class CountryModel extends BaseModel
{
    public function getId(): ?int
    {
        return isset($this->data['ID']) ? (int)$this->data['ID'] : null;
    }

    public function getName(): ?string
    {
        return $this->data['UlkeAdi'] ?? $this->data['Tanim'] ?? null;
    }

    public function getCountryCode(): ?string 
    {
        return $this->data['UlkeKodu'] ?? $this->data['FiltreUlkeKodu'] ?? null;
    }
} 