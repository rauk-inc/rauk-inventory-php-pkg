<?php

namespace RaukInventory\Types;

use DateTimeImmutable;

/**
 * Represents an inventory item in the Rauk Inventory system
 */
readonly class InventoryItem
{
    public function __construct(
        public ?string $hardcode,
        public Entities $entities,
        public Location $currentLocation,
        public ?TransitTo $transitTo,
        /** @var array<string, StatusDetails> */
        public array $availability,
        public string $sku,
        public ?BrandDetails $brandDetails,
        public int $packageQuantity,
        public Color $color,
        public ?FactoryDetails $factoryDetails,
        public Deleted $deleted,
        /** @var LocationHistoryEntry[]|null */
        public ?array $locationHistory,
        public ?DateTimeImmutable $createdAt,
        public ?DateTimeImmutable $updatedAt,
        public string $id,
    ) {}

    /**
     * Create from array (for API responses)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            hardcode: $data['hardcode'] ?? null,
            entities: Entities::fromArray($data['entities']),
            currentLocation: Location::fromArray($data['currentLocation']),
            transitTo: isset($data['transitTo']) ? TransitTo::fromArray($data['transitTo']) : null,
            availability: array_map(
                fn($k, $v) => StatusDetails::fromArray($v),
                array_keys($data['availability'] ?? []),
                array_values($data['availability'] ?? [])
            ),
            sku: $data['sku'],
            brandDetails: isset($data['brandDetails']) ? BrandDetails::fromArray($data['brandDetails']) : null,
            packageQuantity: $data['packageQuantity'],
            color: Color::fromArray($data['color']),
            factoryDetails: isset($data['factoryDetails']) ? FactoryDetails::fromArray($data['factoryDetails']) : null,
            deleted: Deleted::fromArray($data['deleted']),
            locationHistory: array_map(
                fn($entry) => LocationHistoryEntry::fromArray($entry),
                $data['locationHistory'] ?? []
            ),
            createdAt: isset($data['createdAt']) ? new DateTimeImmutable($data['createdAt']) : null,
            updatedAt: isset($data['updatedAt']) ? new DateTimeImmutable($data['updatedAt']) : null,
            id: $data['id'],
        );
    }

    /**
     * Convert to array (for API requests)
     */
    public function toArray(): array
    {
        return [
            'hardcode' => $this->hardcode,
            'entities' => $this->entities->toArray(),
            'currentLocation' => $this->currentLocation->toArray(),
            'transitTo' => $this->transitTo?->toArray(),
            'availability' => array_map(
                fn(StatusDetails $details) => $details->toArray(),
                $this->availability
            ),
            'sku' => $this->sku,
            'brandDetails' => $this->brandDetails?->toArray(),
            'packageQuantity' => $this->packageQuantity,
            'color' => $this->color->toArray(),
            'factoryDetails' => $this->factoryDetails?->toArray(),
            'deleted' => $this->deleted->toArray(),
            'locationHistory' => array_map(
                fn(LocationHistoryEntry $entry) => $entry->toArray(),
                $this->locationHistory ?? []
            ),
            'createdAt' => $this->createdAt?->format('c'),
            'updatedAt' => $this->updatedAt?->format('c'),
            'id' => $this->id,
        ];
    }
}

/**
 * Entity information for an inventory item
 */
readonly class Entities
{
    public function __construct(
        public string $apiId,
        public string $entityId,
        public string $factoryId,
        public string $brandId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            apiId: $data['apiId'],
            entityId: $data['entityId'],
            factoryId: $data['factoryId'],
            brandId: $data['brandId'],
        );
    }

    public function toArray(): array
    {
        return [
            'apiId' => $this->apiId,
            'entityId' => $this->entityId,
            'factoryId' => $this->factoryId,
            'brandId' => $this->brandId,
        ];
    }
}

/**
 * Location information
 */
readonly class Location
{
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?array $details,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            details: $data['details'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'details' => $this->details,
        ], fn($value) => $value !== null);
    }
}

/**
 * Transit destination information
 */
readonly class TransitTo
{
    public function __construct(
        public ?string $id,
        public ?string $client,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            client: $data['client'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'client' => $this->client,
        ], fn($value) => $value !== null);
    }
}

/**
 * Brand details information
 */
readonly class BrandDetails
{
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?string $type,
        public ?string $subType,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            subType: $data['subType'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'subType' => $this->subType,
        ], fn($value) => $value !== null);
    }
}

/**
 * Color information
 */
readonly class Color
{
    public function __construct(
        public ?string $id,
        public string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
        ], fn($value) => $value !== null);
    }
}

/**
 * Factory details information
 */
readonly class FactoryDetails
{
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?string $type,
        public ?string $subType,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            subType: $data['subType'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'subType' => $this->subType,
        ], fn($value) => $value !== null);
    }
}

/**
 * Deletion status information
 */
readonly class Deleted
{
    public function __construct(
        public bool $status,
        public ?DateTimeImmutable $deletionDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            deletionDate: isset($data['deletionDate']) ? new DateTimeImmutable($data['deletionDate']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'deletionDate' => $this->deletionDate?->format('c'),
        ], fn($value) => $value !== null);
    }
}

/**
 * Status details for availability states
 */
readonly class StatusDetails
{
    public function __construct(
        public ?string $orderId,
        public ?DateTimeImmutable $date,
        public ?bool $temporary,
        public ?DateTimeImmutable $expiration,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['orderId'] ?? null,
            date: isset($data['date']) ? new DateTimeImmutable($data['date']) : null,
            temporary: $data['temporary'] ?? null,
            expiration: isset($data['expiration']) ? new DateTimeImmutable($data['expiration']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'orderId' => $this->orderId,
            'date' => $this->date?->format('c'),
            'temporary' => $this->temporary,
            'expiration' => $this->expiration?->format('c'),
        ], fn($value) => $value !== null);
    }
}

/**
 * Location history entry
 */
readonly class LocationHistoryEntry
{
    public function __construct(
        public string $id,
        public string $name,
        public DateTimeImmutable $date,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            date: new DateTimeImmutable($data['date']),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date->format('c'),
        ];
    }
}
