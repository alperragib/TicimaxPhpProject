<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Model\Menu;

use AlperRagib\Ticimax\Model\BaseModel;

/**
 * Class MenuModel
 * Represents a menu item in the Ticimax system
 */
class MenuModel extends BaseModel
{
    public function getId(): int
    {
        return $this->ID ?? 0;
    }

    public function getTitle(): string
    {
        return $this->Baslik ?? '';
    }

    public function isActive(): bool
    {
        return $this->Aktif ?? false;
    }

    public function getContent(): string
    {
        return $this->Icerik ?? '';
    }

    public function getUrl(): string
    {
        return $this->Url ?? '';
    }
} 