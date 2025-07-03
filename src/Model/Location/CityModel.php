<?php

namespace AlperRagib\Ticimax\Model\Location;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class CityModel
 * Represents a city in the Ticimax system.
 */
class CityModel extends BaseModel
{
    public function getId(): ?int
    {
        return isset($this->data['ID']) ? (int)$this->data['ID'] : null;
    }

    public function getName(): ?string
    {
        return $this->data['Tanim'] ?? null;
    }

    public function getCountryId(): ?int
    {
        return isset($this->data['UlkeID']) ? (int)$this->data['UlkeID'] : null;
    }
} 