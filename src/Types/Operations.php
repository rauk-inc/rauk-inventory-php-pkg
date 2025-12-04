<?php

namespace RaukInventory\Types;

use DateTimeImmutable;

/**
 * Operation types for Rauk Inventory API
 */

// Color operation types
readonly class OperationColor
{
    public function __construct(
        public ?string $id,
        public ?string $name,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? null,
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

// Deleted operation types
readonly class OperationDeleted
{
    public function __construct(
        public bool $status,
        public ?string $deletionDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'],
            deletionDate: $data['deletionDate'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'deletionDate' => $this->deletionDate,
        ], fn($value) => $value !== null);
    }
}

// Location History Entry operation types
readonly class OperationLocationHistoryEntry
{
    public function __construct(
        public string $id,
        public string $name,
        public string $date,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            date: $data['date'],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date,
        ];
    }
}

// Status Details operation types
readonly class OperationStatusDetails
{
    public function __construct(
        public ?string $orderId,
        public ?string $date,
        public ?bool $temporary,
        public ?string $expiration,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['orderId'] ?? null,
            date: $data['date'] ?? null,
            temporary: $data['temporary'] ?? null,
            expiration: $data['expiration'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'orderId' => $this->orderId,
            'date' => $this->date,
            'temporary' => $this->temporary,
            'expiration' => $this->expiration,
        ], fn($value) => $value !== null);
    }
}

// Entities operation types
readonly class OperationEntities
{
    public function __construct(
        public ?string $apiId,
        public ?string $entityId,
        public ?string $factoryId,
        public ?string $brandId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            apiId: $data['apiId'] ?? null,
            entityId: $data['entityId'] ?? null,
            factoryId: $data['factoryId'] ?? null,
            brandId: $data['brandId'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'apiId' => $this->apiId,
            'entityId' => $this->entityId,
            'factoryId' => $this->factoryId,
            'brandId' => $this->brandId,
        ], fn($value) => $value !== null);
    }
}

// Location operation types
readonly class OperationLocation
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

// Transit To operation types
readonly class OperationTransitTo
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

// Brand Details operation types
readonly class OperationBrandDetails
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

// Factory Details operation types
readonly class OperationFactoryDetails
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

// Availability operation types
readonly class OperationAvailability
{
    public function __construct(
        public ?OperationStatusDetails $produced,
        public ?OperationStatusDetails $reserved,
        public ?OperationStatusDetails $sold,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            produced: isset($data['produced']) ? OperationStatusDetails::fromArray($data['produced']) : null,
            reserved: isset($data['reserved']) ? OperationStatusDetails::fromArray($data['reserved']) : null,
            sold: isset($data['sold']) ? OperationStatusDetails::fromArray($data['sold']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'produced' => $this->produced?->toArray(),
            'reserved' => $this->reserved?->toArray(),
            'sold' => $this->sold?->toArray(),
        ], fn($value) => $value !== null);
    }
}

// Base Item operation types (for create/update operations)
readonly class OperationBaseItem
{
    public function __construct(
        public ?string $hardcode,
        public ?OperationEntities $entities,
        public ?OperationLocation $currLoc,
        public ?OperationTransitTo $transitTo,
        public ?OperationAvailability $availability,
        public ?string $sku,
        public ?int $qty,
        public ?OperationColor $color,
        public ?OperationBrandDetails $brandDetails,
        public ?OperationFactoryDetails $factoryDetails,
        public ?OperationDeleted $deleted,
        /** @var OperationLocationHistoryEntry[]|null */
        public ?array $locationHistory,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            hardcode: $data['hardcode'] ?? null,
            entities: isset($data['entities']) ? OperationEntities::fromArray($data['entities']) : null,
            currLoc: isset($data['currLoc']) ? OperationLocation::fromArray($data['currLoc']) : null,
            transitTo: isset($data['transitTo']) ? OperationTransitTo::fromArray($data['transitTo']) : null,
            availability: isset($data['availability']) ? OperationAvailability::fromArray($data['availability']) : null,
            sku: $data['sku'] ?? null,
            qty: $data['qty'] ?? null,
            color: isset($data['color']) ? OperationColor::fromArray($data['color']) : null,
            brandDetails: isset($data['brandDetails']) ? OperationBrandDetails::fromArray($data['brandDetails']) : null,
            factoryDetails: isset($data['factoryDetails']) ? OperationFactoryDetails::fromArray($data['factoryDetails']) : null,
            deleted: isset($data['deleted']) ? OperationDeleted::fromArray($data['deleted']) : null,
            locationHistory: array_map(
                fn($entry) => OperationLocationHistoryEntry::fromArray($entry),
                $data['locationHistory'] ?? []
            ),
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'hardcode' => $this->hardcode,
            'entities' => $this->entities?->toArray(),
            'currLoc' => $this->currLoc?->toArray(),
            'transitTo' => $this->transitTo?->toArray(),
            'availability' => $this->availability?->toArray(),
            'sku' => $this->sku,
            'qty' => $this->qty,
            'color' => $this->color?->toArray(),
            'brandDetails' => $this->brandDetails?->toArray(),
            'factoryDetails' => $this->factoryDetails?->toArray(),
            'deleted' => $this->deleted?->toArray(),
            'locationHistory' => array_map(
                fn(OperationLocationHistoryEntry $entry) => $entry->toArray(),
                $this->locationHistory ?? []
            ),
        ], fn($value) => $value !== null);
    }
}

// Create operation types
readonly class OperationCreateItem extends OperationBaseItem
{
    public function __construct(
        ?string $hardcode,
        ?OperationEntities $entities,
        OperationLocation $currLoc,
        ?OperationTransitTo $transitTo,
        ?OperationAvailability $availability,
        string $sku,
        int $qty,
        OperationColor $color,
        OperationBrandDetails $brandDetails,
        OperationFactoryDetails $factoryDetails,
        ?OperationDeleted $deleted,
        ?array $locationHistory,
    ) {
        parent::__construct(
            hardcode: $hardcode,
            entities: $entities,
            currLoc: $currLoc,
            transitTo: $transitTo,
            availability: $availability,
            sku: $sku,
            qty: $qty,
            color: $color,
            brandDetails: $brandDetails,
            factoryDetails: $factoryDetails,
            deleted: $deleted,
            locationHistory: $locationHistory,
        );
    }

    public static function fromArray(array $data): self
    {
        $base = parent::fromArray($data);

        return new self(
            hardcode: $base->hardcode,
            entities: $base->entities,
            currLoc: $base->currLoc ?? throw new \InvalidArgumentException('currLoc is required for create operation'),
            transitTo: $base->transitTo,
            availability: $base->availability,
            sku: $base->sku ?? throw new \InvalidArgumentException('sku is required for create operation'),
            qty: $base->qty ?? throw new \InvalidArgumentException('qty is required for create operation'),
            color: $base->color ?? throw new \InvalidArgumentException('color is required for create operation'),
            brandDetails: $base->brandDetails ?? throw new \InvalidArgumentException('brandDetails is required for create operation'),
            factoryDetails: $base->factoryDetails ?? throw new \InvalidArgumentException('factoryDetails is required for create operation'),
            deleted: $base->deleted,
            locationHistory: $base->locationHistory,
        );
    }
}

// Query operation types for filtering
readonly class OperationQueryColor
{
    public function __construct(
        public ?string $name,
        public ?string $id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? null,
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'id' => $this->id,
        ], fn($value) => $value !== null);
    }
}

readonly class OperationQueryDeleted
{
    public function __construct(
        public ?bool $status,
        public ?string $deletionDate,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            deletionDate: $data['deletionDate'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'status' => $this->status,
            'deletionDate' => $this->deletionDate,
        ], fn($value) => $value !== null);
    }
}

readonly class OperationQueryEntities
{
    public function __construct(
        public ?string $apiId,
        public ?string $entityId,
        public ?string $factoryId,
        public ?string $brandId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            apiId: $data['apiId'] ?? null,
            entityId: $data['entityId'] ?? null,
            factoryId: $data['factoryId'] ?? null,
            brandId: $data['brandId'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'apiId' => $this->apiId,
            'entityId' => $this->entityId,
            'factoryId' => $this->factoryId,
            'brandId' => $this->brandId,
        ], fn($value) => $value !== null);
    }
}

readonly class OperationQueryLocation
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

readonly class OperationQueryTransitTo
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

readonly class OperationQueryBrandDetails
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

readonly class OperationQueryFactoryDetails
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

readonly class OperationQueryStatusDetails
{
    public function __construct(
        public ?string $orderId,
        public ?string $date,
        public ?bool $temporary,
        public ?string $expiration,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            orderId: $data['orderId'] ?? null,
            date: $data['date'] ?? null,
            temporary: $data['temporary'] ?? null,
            expiration: $data['expiration'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'orderId' => $this->orderId,
            'date' => $this->date,
            'temporary' => $this->temporary,
            'expiration' => $this->expiration,
        ], fn($value) => $value !== null);
    }
}

readonly class OperationQueryAvailability
{
    public function __construct(
        public ?OperationQueryStatusDetails $produced,
        public ?OperationQueryStatusDetails $reserved,
        public ?OperationQueryStatusDetails $sold,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            produced: isset($data['produced']) ? OperationQueryStatusDetails::fromArray($data['produced']) : null,
            reserved: isset($data['reserved']) ? OperationQueryStatusDetails::fromArray($data['reserved']) : null,
            sold: isset($data['sold']) ? OperationQueryStatusDetails::fromArray($data['sold']) : null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'produced' => $this->produced?->toArray(),
            'reserved' => $this->reserved?->toArray(),
            'sold' => $this->sold?->toArray(),
        ], fn($value) => $value !== null);
    }
}

// Query type - this is the main query interface
readonly class OperationQuery
{
    public function __construct(
        public ?OperationQueryColor $color,
        public ?OperationQueryDeleted $deleted,
        public ?OperationQueryEntities $entities,
        public ?OperationQueryLocation $currLoc,
        public ?OperationQueryTransitTo $transitTo,
        public ?OperationQueryBrandDetails $brandDetails,
        public ?OperationQueryFactoryDetails $factoryDetails,
        public ?OperationQueryAvailability $availability,
        public ?string $sku,
        public ?int $qty,
        public ?string $hardcode,
        public ?string $id,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            color: isset($data['color']) ? OperationQueryColor::fromArray($data['color']) : null,
            deleted: isset($data['deleted']) ? OperationQueryDeleted::fromArray($data['deleted']) : null,
            entities: isset($data['entities']) ? OperationQueryEntities::fromArray($data['entities']) : null,
            currLoc: isset($data['currLoc']) ? OperationQueryLocation::fromArray($data['currLoc']) : null,
            transitTo: isset($data['transitTo']) ? OperationQueryTransitTo::fromArray($data['transitTo']) : null,
            brandDetails: isset($data['brandDetails']) ? OperationQueryBrandDetails::fromArray($data['brandDetails']) : null,
            factoryDetails: isset($data['factoryDetails']) ? OperationQueryFactoryDetails::fromArray($data['factoryDetails']) : null,
            availability: isset($data['availability']) ? OperationQueryAvailability::fromArray($data['availability']) : null,
            sku: $data['sku'] ?? null,
            qty: $data['qty'] ?? null,
            hardcode: $data['hardcode'] ?? null,
            id: $data['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'color' => $this->color?->toArray(),
            'deleted' => $this->deleted?->toArray(),
            'entities' => $this->entities?->toArray(),
            'currLoc' => $this->currLoc?->toArray(),
            'transitTo' => $this->transitTo?->toArray(),
            'brandDetails' => $this->brandDetails?->toArray(),
            'factoryDetails' => $this->factoryDetails?->toArray(),
            'availability' => $this->availability?->toArray(),
            'sku' => $this->sku,
            'qty' => $this->qty,
            'hardcode' => $this->hardcode,
            'id' => $this->id,
        ], fn($value) => $value !== null);
    }
}

// Bulk Write operation types
readonly class OperationUpdateOne
{
    public function __construct(
        public OperationQuery $filter,
        public OperationUpdateItem $update,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filter: OperationQuery::fromArray($data['filter']),
            update: OperationUpdateItem::fromArray($data['update']),
        );
    }

    public function toArray(): array
    {
        return [
            'filter' => $this->filter->toArray(),
            'update' => $this->update->toArray(),
        ];
    }
}

readonly class OperationInsertOne
{
    public function __construct(
        public OperationCreateItem $document,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            document: OperationCreateItem::fromArray($data['document']),
        );
    }

    public function toArray(): array
    {
        return [
            'document' => $this->document->toArray(),
        ];
    }
}

readonly class OperationDeleteOne
{
    public function __construct(
        public OperationQuery $filter,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filter: OperationQuery::fromArray($data['filter']),
        );
    }

    public function toArray(): array
    {
        return [
            'filter' => $this->filter->toArray(),
        ];
    }
}

readonly class OperationReplaceOne
{
    public function __construct(
        public OperationQuery $filter,
        public OperationCreateItem $replacement,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            filter: OperationQuery::fromArray($data['filter']),
            replacement: OperationCreateItem::fromArray($data['replacement']),
        );
    }

    public function toArray(): array
    {
        return [
            'filter' => $this->filter->toArray(),
            'replacement' => $this->replacement->toArray(),
        ];
    }
}

// Note: OperationBulkWrite is represented as an array of mixed operation objects in PHP
// Supported operations: OperationUpdateOne, OperationInsertOne, OperationDeleteOne, OperationReplaceOne

// Aggregate operation types
readonly class OperationMatchStage
{
    public function __construct(
        public OperationQuery $match,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            match: OperationQuery::fromArray($data['$match']),
        );
    }

    public function toArray(): array
    {
        return ['$match' => $this->match->toArray()];
    }
}

readonly class OperationGroupStage
{
    public function __construct(
        public ?string $id,
        public array $group,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['$group']['_id'] ?? null,
            group: $data['$group'],
        );
    }

    public function toArray(): array
    {
        return ['$group' => $this->group];
    }
}

readonly class OperationSortStage
{
    public function __construct(
        public array $sort,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            sort: $data['$sort'],
        );
    }

    public function toArray(): array
    {
        return ['$sort' => $this->sort];
    }
}

readonly class OperationProjectStage
{
    public function __construct(
        public array $project,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            project: $data['$project'],
        );
    }

    public function toArray(): array
    {
        return ['$project' => $this->project];
    }
}

readonly class OperationLimitStage
{
    public function __construct(
        public int $limit,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            limit: $data['$limit'],
        );
    }

    public function toArray(): array
    {
        return ['$limit' => $this->limit];
    }
}

readonly class OperationSkipStage
{
    public function __construct(
        public int $skip,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            skip: $data['$skip'],
        );
    }

    public function toArray(): array
    {
        return ['$skip' => $this->skip];
    }
}

readonly class OperationUnwindStage
{
    public function __construct(
        public string|array $unwind,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            unwind: $data['$unwind'],
        );
    }

    public function toArray(): array
    {
        return ['$unwind' => $this->unwind];
    }
}

readonly class OperationAddFieldsStage
{
    public function __construct(
        public array $addFields,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            addFields: $data['$addFields'],
        );
    }

    public function toArray(): array
    {
        return ['$addFields' => $this->addFields];
    }
}

readonly class OperationCountStage
{
    public function __construct(
        public string $count,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            count: $data['$count'],
        );
    }

    public function toArray(): array
    {
        return ['$count' => $this->count];
    }
}

// Note: In PHP, OperationAggregatePipeline is an array of mixed aggregate stage objects
// Supported stages: OperationMatchStage, OperationGroupStage, OperationSortStage, OperationProjectStage,
// OperationLimitStage, OperationSkipStage, OperationUnwindStage, OperationAddFieldsStage, OperationCountStage

// Request Options types
readonly class OperationRequestOptions
{
    public function __construct(
        /** @var array<string, 0|1> */
        public ?array $select,
        public ?int $limit,
        /** @var array<string, 1|-1> */
        public ?array $sort,
        public ?bool $includeDeleted,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            select: $data['select'] ?? null,
            limit: $data['limit'] ?? null,
            sort: $data['sort'] ?? null,
            includeDeleted: $data['includeDeleted'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'select' => $this->select,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'includeDeleted' => $this->includeDeleted,
        ], fn($value) => $value !== null);
    }
}

readonly class OperationIncludeDeletedOnly
{
    public function __construct(
        public ?bool $includeDeleted,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            includeDeleted: $data['includeDeleted'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'includeDeleted' => $this->includeDeleted,
        ], fn($value) => $value !== null);
    }
}

// Response types
readonly class OperationDeleteResult
{
    public function __construct(
        public int $deletedCount,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            deletedCount: $data['deletedCount'],
        );
    }

    public function toArray(): array
    {
        return [
            'deletedCount' => $this->deletedCount,
        ];
    }
}

readonly class OperationUpdateResult
{
    public function __construct(
        public int $matchedCount,
        public int $modifiedCount,
        public bool $acknowledged,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            matchedCount: $data['matchedCount'],
            modifiedCount: $data['modifiedCount'],
            acknowledged: $data['acknowledged'],
        );
    }

    public function toArray(): array
    {
        return [
            'matchedCount' => $this->matchedCount,
            'modifiedCount' => $this->modifiedCount,
            'acknowledged' => $this->acknowledged,
        ];
    }
}

readonly class OperationInsertResult
{
    public function __construct(
        public bool $acknowledged,
        public string $insertedId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            acknowledged: $data['acknowledged'],
            insertedId: $data['insertedId'],
        );
    }

    public function toArray(): array
    {
        return [
            'acknowledged' => $this->acknowledged,
            'insertedId' => $this->insertedId,
        ];
    }
}

// I need to create OperationUpdateItem class
readonly class OperationUpdateItem
{
    public function __construct(
        public ?OperationQueryColor $color,
        public ?OperationQueryDeleted $deleted,
        public ?OperationQueryEntities $entities,
        public ?OperationQueryLocation $currLoc,
        public ?OperationQueryTransitTo $transitTo,
        public ?OperationQueryBrandDetails $brandDetails,
        public ?OperationQueryFactoryDetails $factoryDetails,
        public ?OperationQueryAvailability $availability,
        public ?string $sku,
        public ?int $qty,
        public ?string $hardcode,
        public ?string $id,
        /** @var array<string, mixed> */
        public ?array $set,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            color: isset($data['color']) ? OperationQueryColor::fromArray($data['color']) : null,
            deleted: isset($data['deleted']) ? OperationQueryDeleted::fromArray($data['deleted']) : null,
            entities: isset($data['entities']) ? OperationQueryEntities::fromArray($data['entities']) : null,
            currLoc: isset($data['currLoc']) ? OperationQueryLocation::fromArray($data['currLoc']) : null,
            transitTo: isset($data['transitTo']) ? OperationQueryTransitTo::fromArray($data['transitTo']) : null,
            brandDetails: isset($data['brandDetails']) ? OperationQueryBrandDetails::fromArray($data['brandDetails']) : null,
            factoryDetails: isset($data['factoryDetails']) ? OperationQueryFactoryDetails::fromArray($data['factoryDetails']) : null,
            availability: isset($data['availability']) ? OperationQueryAvailability::fromArray($data['availability']) : null,
            sku: $data['sku'] ?? null,
            qty: $data['qty'] ?? null,
            hardcode: $data['hardcode'] ?? null,
            id: $data['id'] ?? null,
            set: $data['$set'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'color' => $this->color?->toArray(),
            'deleted' => $this->deleted?->toArray(),
            'entities' => $this->entities?->toArray(),
            'currLoc' => $this->currLoc?->toArray(),
            'transitTo' => $this->transitTo?->toArray(),
            'brandDetails' => $this->brandDetails?->toArray(),
            'factoryDetails' => $this->factoryDetails?->toArray(),
            'availability' => $this->availability?->toArray(),
            'sku' => $this->sku,
            'qty' => $this->qty,
            'hardcode' => $this->hardcode,
            'id' => $this->id,
            '$set' => $this->set,
        ], fn($value) => $value !== null);
    }
}

// Export all operation types
class OperationTypes
{
    public const CreateItem = OperationCreateItem::class;
    public const UpdateItem = OperationUpdateItem::class;
    public const QueryItem = OperationQuery::class;
    public const QueryDto = OperationQuery::class;
    public const BulkWrite = 'OperationBulkWrite';
    public const BulkOperation = 'OperationBulkOperation';
    public const UpdateOne = OperationUpdateOne::class;
    public const InsertOne = OperationInsertOne::class;
    public const DeleteOne = OperationDeleteOne::class;
    public const ReplaceOne = OperationReplaceOne::class;
    public const AggregateStage = 'OperationAggregateStage';
    public const AggregatePipeline = 'OperationAggregatePipeline';
    public const RequestOptions = OperationRequestOptions::class;
    public const IncludeDeletedOnly = OperationIncludeDeletedOnly::class;
    public const DeleteResult = OperationDeleteResult::class;
    public const UpdateResult = OperationUpdateResult::class;
    public const InsertResult = OperationInsertResult::class;
}
