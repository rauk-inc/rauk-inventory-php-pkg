<?php

require_once __DIR__ . '/vendor/autoload.php';

use RaukInventory\RaukInventory;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationUpdateItem;
use RaukInventory\Types\OperationRequestOptions;
use RaukInventory\Types\OperationColor;
use RaukInventory\Types\OperationBrandDetails;
use RaukInventory\Types\OperationFactoryDetails;
use RaukInventory\Types\OperationEntities;
use RaukInventory\Types\OperationLocation;

// Example usage of the RaukInventory PHP SDK

try {
    // Initialize the RaukInventory client
    $raukInventory = new RaukInventory([
        'apiKeyId' => 'your-api-key-id',
        'apiSecret' => 'your-api-secret',
        'apiPublicKey' => 'your-api-public-key',
        'apiBaseUrl' => 'https://inventory.rauk.app' // optional
    ]);

    // Example 1: Create a new inventory item
    echo "Creating inventory item...\n";

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

    $options = new OperationRequestOptions(
        select: ['sku' => 1, 'color' => 1],
        limit: null,
        sort: null,
        includeDeleted: null
    );

    // Uncomment to actually create the item (requires valid API credentials)
    // $createdItem = RaukInventory::create($newItem, $options);
    // echo "Created item with ID: " . $createdItem->id . "\n";

    // Example 2: Find items by SKU
    echo "\nFinding items by SKU...\n";

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

    // Uncomment to actually search (requires valid API credentials)
    // $items = RaukInventory::find($query);
    // echo "Found " . count($items) . " items\n";
    // foreach ($items as $item) {
    //     echo "Item: " . $item->sku . " - " . $item->color->name . "\n";
    // }

    // Example 3: Update an item
    echo "\nUpdating item...\n";

    $updateQuery = new OperationQuery(
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

    $updateItem = new OperationUpdateItem(
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

    // Uncomment to actually update (requires valid API credentials)
    // $updateResult = RaukInventory::update($updateQuery, $updateItem);
    // echo "Updated " . $updateResult->modifiedCount . " items\n";

    // Example 4: Find one item
    echo "\nFinding one item...\n";

    // Uncomment to actually find one item (requires valid API credentials)
    // $item = RaukInventory::findOne($query);
    // if ($item !== null) {
    //     echo "Found item: " . $item->sku . " at " . $item->currentLocation->name . "\n";
    // } else {
    //     echo "Item not found\n";
    // }

    echo "\nPHP SDK examples completed successfully!\n";
    echo "Note: Uncomment the API calls above to actually interact with the Rauk Inventory API.\n";
    echo "Make sure to provide valid API credentials in the RaukInventory constructor.\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if ($e instanceof \RaukInventory\Utils\RaukError) {
        echo "Rauk Error Details:\n";
        echo "Status Code: " . $e->statusCode . "\n";
        echo "Timestamp: " . $e->timestamp . "\n";
        if ($e->context !== null) {
            echo "Context: " . json_encode($e->context, JSON_PRETTY_PRINT) . "\n";
        }
    }
}
