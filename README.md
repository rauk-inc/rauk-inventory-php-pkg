# RaukInventory PHP SDK

A modern PHP SDK for the Rauk Inventory API, providing a type-safe and convenient interface for managing inventory items.

## Features

- **Type Safety**: Full type definitions using PHP 8.3+ features (readonly classes, typed properties, union types)
- **Modern PHP**: Built with PHP 8.3+ features including:
  - Readonly classes and properties
  - Named parameters
  - Union types
  - Modern array syntax
- **Complete CRUD Operations**: Create, Read, Update, Delete operations
- **Advanced Querying**: Complex queries with MongoDB-style operators
- **Bulk Operations**: Batch operations for efficient data management
- **Aggregation Support**: MongoDB aggregation pipeline support
- **Error Handling**: Comprehensive error handling with custom exception types
- **Request Signing**: HMAC-SHA256 request authentication
- **Singleton Pattern**: Easy-to-use static interface with singleton pattern

## Requirements

- PHP 8.3 or higher
- `ext-curl` extension
- `ext-json` extension

## Installation

```bash
composer require rauk-inventory/rauk-inventory-php
```

## Quick Start

### Basic Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use RaukInventory\RaukInventory;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationEntities;
use RaukInventory\Types\OperationLocation;
use RaukInventory\Types\OperationColor;
use RaukInventory\Types\OperationBrandDetails;
use RaukInventory\Types\OperationFactoryDetails;

// Initialize the client
$raukInventory = new RaukInventory([
    'apiKeyId' => 'your-api-key-id',
    'apiSecret' => 'your-api-secret',
    'apiPublicKey' => 'your-api-public-key',
    'apiBaseUrl' => 'https://inventory.rauk.app' // optional
]);

// Create a new inventory item
$newItem = new OperationCreateItem(
    hardcode: null,
    entities: new OperationEntities(
        apiId: 'item-api-123',
        entityId: 'item-entity-456',
        factoryId: 'factory-789',
        brandId: 'brand-101'
    ),
    currentLocation: new OperationLocation(
        id: 'warehouse-1',
        name: 'Main Warehouse',
        details: null
    ),
    transitTo: null,
    availability: null,
    sku: 'ITEM-001',
    packageQuantity: 10,
    color: new OperationColor(
        id: 'color-123',
        name: 'Red'
    ),
    brandDetails: new OperationBrandDetails(
        id: 'brand-101',
        name: 'Premium Brand',
        type: 'luxury',
        subType: null
    ),
    factoryDetails: new OperationFactoryDetails(
        id: 'factory-789',
        name: 'Main Factory',
        type: 'manufacturing',
        subType: null
    ),
    deleted: null,
    locationHistory: null
);

$createdItem = RaukInventory::create($newItem);
echo "Created item with ID: " . $createdItem->id . "\n";
```

### Querying Items

```php
// Find items by SKU
$query = new OperationQuery(
    color: null,
    deleted: null,
    entities: null,
    currentLocation: null,
    transitTo: null,
    brandDetails: null,
    factoryDetails: null,
    availability: null,
    sku: 'ITEM-001',
    packageQuantity: null,
    hardcode: null,
    id: null
);

$items = RaukInventory::find($query);
foreach ($items as $item) {
    echo "Item: " . $item->sku . " - " . $item->color->name . "\n";
}

// Find a single item
$item = RaukInventory::findOne($query);
if ($item !== null) {
    echo "Found item: " . $item->sku . "\n";
}
```

### Updating Items

```php
use RaukInventory\Types\OperationUpdateItem;

$query = new OperationQuery(
    sku: 'ITEM-001',
    // ... other fields
);

$update = new OperationUpdateItem(
    color: null,
    deleted: null,
    entities: null,
    currentLocation: null,
    transitTo: null,
    brandDetails: null,
    factoryDetails: null,
    availability: null,
    sku: null,
    packageQuantity: null,
    hardcode: null,
    id: null,
    set: [
        'packageQuantity' => 20,
        'currentLocation' => [
            'id' => 'warehouse-2',
            'name' => 'Secondary Warehouse'
        ]
    ]
);

$result = RaukInventory::update($query, $update);
echo "Updated " . $result->modifiedCount . " items\n";
```

### Advanced Operations

```php
use RaukInventory\Types\OperationRequestOptions;

// With options (limit, sort, select)
$options = new OperationRequestOptions(
    select: ['sku' => 1, 'color' => 1],
    limit: 10,
    sort: ['createdAt' => -1],
    includeDeleted: false
);

$items = RaukInventory::find($query, $options);

// Batch updates
$batchUpdates = [
    [new OperationQuery(sku: 'ITEM-001'), new OperationUpdateItem(set: ['packageQuantity' => 15])],
    [new OperationQuery(sku: 'ITEM-002'), new OperationUpdateItem(set: ['currentLocation' => ['id' => 'warehouse-3']])]
];

RaukInventory::updateBatch($batchUpdates);

// Aggregation
use RaukInventory\Types\OperationAggregatePipeline;
use RaukInventory\Types\OperationMatchStage;
use RaukInventory\Types\OperationGroupStage;

$pipeline = new OperationAggregatePipeline([
    new OperationMatchStage(new OperationQuery(factoryId: 'factory-789')),
    new OperationGroupStage(
        id: '$sku',
        group: ['_id' => '$sku', 'count' => ['$sum' => 1]]
    )
]);

$results = RaukInventory::aggregate($pipeline);
```

## API Reference

### Core Classes

- `RaukInventory` - Main class with static methods
- `RaukInventoryClient` - Core client class (for direct instantiation)
- `InventoryItem` - Represents an inventory item
- `OperationQuery` - Query builder for filtering
- `OperationCreateItem` - Data structure for creating items
- `OperationUpdateItem` - Data structure for updates

### Error Handling

The SDK throws specific exception types:

- `RaukValidationError` - Validation errors from API
- `RaukAuthenticationError` - Authentication/authorization errors
- `RaukNetworkError` - Network/connection errors
- `RaukApiError` - Generic API errors

```php
try {
    $item = RaukInventory::create($newItem);
} catch (RaukInventory\Utils\RaukValidationError $e) {
    echo "Validation errors: " . implode(', ', $e->getAllMessages()) . "\n";
} catch (RaukInventory\Utils\RaukError $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

## Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
composer phpstan
```

### Full Check

```bash
composer check
```

## Type System

The PHP SDK uses modern PHP type system features:

- **Readonly Classes**: All data classes are readonly for immutability
- **Named Parameters**: Constructor parameters can be passed by name
- **Union Types**: Support for `string|null`, `int|string`, etc.
- **Generic Arrays**: Properly typed array properties
- **Enum-like Types**: Where appropriate for status values

## Architecture

The SDK follows a layered architecture:

1. **Types Layer** (`src/Types/`) - Data structures and type definitions
2. **Core Layer** (`src/Core/`) - HTTP client and low-level operations
3. **Utils Layer** (`src/Utils/`) - Utilities like request signing and error handling
4. **Main Layer** (`src/`) - Main `RaukInventory` class with static interface

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if necessary
5. Run the test suite: `composer test`
6. Run static analysis: `composer phpstan`
7. Submit a pull request

## License

MIT License - see LICENSE file for details.
