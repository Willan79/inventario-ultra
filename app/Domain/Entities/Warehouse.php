<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;
use InvalidArgumentException;

class Warehouse
{
    private ?int $id = null;
    private string $uuid;
    private string $code;
    private string $name;
    private ?string $location;
    private bool $isActive;
    private ?int $managerId;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $uuid,
        string $code,
        string $name,
        ?string $location = null,
        bool $isActive = true,
        ?int $managerId = null
    ) {
        $this->uuid = $uuid;
        $this->code = strtoupper($code);
        $this->name = $name;
        $this->location = $location;
        $this->isActive = $isActive;
        $this->managerId = $managerId;
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

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getManagerId(): ?int
    {
        return $this->managerId;
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
        ?string $location = null,
        ?bool $isActive = null,
        ?int $managerId = null
    ): self {
        if ($name !== null) $this->name = $name;
        if ($location !== null) $this->location = $location;
        if ($isActive !== null) $this->isActive = $isActive;
        if ($managerId !== null) $this->managerId = $managerId;
        
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function deactivate(): self
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function activate(): self
    {
        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
