<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Category
{
    private ?int $id = null;
    private string $uuid;
    private string $name;
    private ?string $description;
    private ?int $parentId;
    private bool $isActive;
    private int $sortOrder;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $uuid,
        string $name,
        ?string $description = null,
        ?int $parentId = null,
        bool $isActive = true,
        int $sortOrder = 0
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->description = $description;
        $this->parentId = $parentId;
        $this->isActive = $isActive;
        $this->sortOrder = $sortOrder;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function update(
        ?string $name = null,
        ?string $description = null,
        ?int $parentId = null,
        ?bool $isActive = null,
        ?int $sortOrder = null
    ): self {
        if ($name !== null) $this->name = $name;
        if ($description !== null) $this->description = $description;
        if ($parentId !== null) $this->parentId = $parentId;
        if ($isActive !== null) $this->isActive = $isActive;
        if ($sortOrder !== null) $this->sortOrder = $sortOrder;
        
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
