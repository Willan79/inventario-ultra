<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class Supplier
{
    private ?int $id = null;
    private string $uuid;
    private string $name;
    private ?string $contactName;
    private ?string $email;
    private ?string $phone;
    private ?string $address;
    private ?string $taxId;
    private bool $isActive;
    private ?string $notes;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $uuid,
        string $name,
        ?string $contactName = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $address = null,
        ?string $taxId = null,
        bool $isActive = true,
        ?string $notes = null
    ) {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->contactName = $contactName;
        $this->email = $email;
        $this->phone = $phone;
        $this->address = $address;
        $this->taxId = $taxId;
        $this->isActive = $isActive;
        $this->notes = $notes;
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

    public function getContactName(): ?string
    {
        return $this->contactName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
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
        ?string $contactName = null,
        ?string $email = null,
        ?string $phone = null,
        ?string $address = null,
        ?string $taxId = null,
        ?bool $isActive = null,
        ?string $notes = null
    ): self {
        if ($name !== null) $this->name = $name;
        if ($contactName !== null) $this->contactName = $contactName;
        if ($email !== null) $this->email = $email;
        if ($phone !== null) $this->phone = $phone;
        if ($address !== null) $this->address = $address;
        if ($taxId !== null) $this->taxId = $taxId;
        if ($isActive !== null) $this->isActive = $isActive;
        if ($notes !== null) $this->notes = $notes;
        
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
