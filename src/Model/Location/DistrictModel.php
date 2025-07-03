<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Location;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class DistrictModel
 * Represents a district (ilçe) in the Ticimax system.
 */
class DistrictModel extends BaseModel
{
    public function getId(): ?int
    {
        return isset($this->data['ID']) ? (int)$this->data['ID'] : null;
    }

    public function getName(): ?string
    {
        return $this->data['Tanim'] ?? null;
    }

    public function getCityId(): ?int
    {
        return isset($this->data['IlID']) ? (int)$this->data['IlID'] : null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
} 