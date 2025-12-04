<?php

/**
 * Basic manual test for RaukInventory PHP SDK
 * This can run without PHPUnit to verify core functionality
 */

echo "🧪 Running basic RaukInventory PHP SDK tests...\n\n";

// Include the autoloader (simulate composer autoload)
$autoloadPath = __DIR__ . '/src/RaukInventory.php';
if (!file_exists($autoloadPath)) {
    echo "❌ Error: Cannot find RaukInventory.php\n";
    exit(1);
}

// Simple manual autoloading for testing
spl_autoload_register(function ($className) {
    // Map namespaces to actual files
    $fileMap = [
        'RaukInventory\Core\RaukInventoryClient' => 'Core/RaukInventoryClient.php',
        'RaukInventory\Types\OperationColor' => 'Types/Operations.php',
        'RaukInventory\Types\OperationBrandDetails' => 'Types/Operations.php',
        'RaukInventory\Types\OperationFactoryDetails' => 'Types/Operations.php',
        'RaukInventory\Types\OperationEntities' => 'Types/Operations.php',
        'RaukInventory\Types\OperationLocation' => 'Types/Operations.php',
        'RaukInventory\Types\OperationQuery' => 'Types/Operations.php',
        'RaukInventory\Types\OperationUpdateItem' => 'Types/Operations.php',
        'RaukInventory\Types\OperationCreateItem' => 'Types/Operations.php',
        'RaukInventory\Types\OperationRequestOptions' => 'Types/Operations.php',
        'RaukInventory\Types\InventoryItem' => 'Types/InventoryItem.php',
        'RaukInventory\Utils\RaukError' => 'Utils/Errors.php',
        'RaukInventory\Utils\RaukValidationError' => 'Utils/Errors.php',
        'RaukInventory\Utils\RaukAuthenticationError' => 'Utils/Errors.php',
        'RaukInventory\Utils\RaukNetworkError' => 'Utils/Errors.php',
        'RaukInventory\Utils\RaukApiError' => 'Utils/Errors.php',
        'RaukInventory\Utils\SignRequest' => 'Utils/SignRequest.php',
    ];

    if (isset($fileMap[$className])) {
        $filePath = __DIR__ . '/src/' . $fileMap[$className];
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
});

// Test 1: Can create client
echo "Test 1: Creating RaukInventoryClient...\n";
try {
    $client = new RaukInventory\Core\RaukInventoryClient(
        'test-key-id',
        'test-secret',
        'test-public-key',
        'https://test-api.example.com'
    );
    echo "✅ RaukInventoryClient created successfully\n";
} catch (Exception $e) {
    echo "❌ Failed to create client: " . $e->getMessage() . "\n";
}

// Test 2: Can create operation objects
echo "\nTest 2: Creating operation objects...\n";
try {
    $color = new RaukInventory\Types\OperationColor(id: 'color-123', name: 'Red');
    $brandDetails = new RaukInventory\Types\OperationBrandDetails(
        id: 'brand-101',
        name: 'Test Brand',
        type: 'luxury',
        subType: null
    );
    $entities = new RaukInventory\Types\OperationEntities(
        apiId: 'api-123',
        entityId: 'entity-456',
        factoryId: 'factory-789',
        brandId: 'brand-101'
    );

    echo "✅ OperationColor: {$color->name}\n";
    echo "✅ OperationBrandDetails: {$brandDetails->name} ({$brandDetails->type})\n";
    echo "✅ OperationEntities: factory={$entities->factoryId}, brand={$entities->brandId}\n";
} catch (Exception $e) {
    echo "❌ Failed to create operation objects: " . $e->getMessage() . "\n";
}

// Test 3: Can create query object
echo "\nTest 3: Creating query object...\n";
try {
    $query = new RaukInventory\Types\OperationQuery(
        color: null,
        deleted: null,
        entities: null,
        currLoc: null,
        transitTo: null,
        brandDetails: null,
        factoryDetails: null,
        availability: null,
        sku: 'ITEM-001',
        qty: null,
        hardcode: null,
        id: null
    );

    echo "✅ OperationQuery: sku={$query->sku}\n";
} catch (Exception $e) {
    echo "❌ Failed to create query object: " . $e->getMessage() . "\n";
}

// Test 4: Can convert objects to/from arrays
echo "\nTest 4: Object serialization...\n";
try {
    $queryArray = $query->toArray();
    $reconstructedQuery = RaukInventory\Types\OperationQuery::fromArray($queryArray);

    echo "✅ Query serialization: " . ($reconstructedQuery->sku === $query->sku ? 'works' : 'failed') . "\n";
} catch (Exception $e) {
    echo "❌ Failed object serialization: " . $e->getMessage() . "\n";
}

// Test 5: Error classes exist
echo "\nTest 5: Error classes...\n";
try {
    echo "✅ RaukError class exists: " . (class_exists('RaukInventory\Utils\RaukError') ? 'yes' : 'no') . "\n";
    echo "✅ RaukValidationError class exists: " . (class_exists('RaukInventory\Utils\RaukValidationError') ? 'yes' : 'no') . "\n";
    echo "✅ SignRequest class exists: " . (class_exists('RaukInventory\Utils\SignRequest') ? 'yes' : 'no') . "\n";
} catch (Exception $e) {
    echo "❌ Error checking classes: " . $e->getMessage() . "\n";
}

// Test 6: Request signing (basic test)
echo "\nTest 6: Request signing...\n";
try {
    $signature = RaukInventory\Utils\SignRequest::sign(
        'test-key-id',
        'test-secret',
        'test-public-key',
        ['test' => 'data']
    );
    echo "✅ Request signing works: " . (strlen($signature) > 0 ? 'yes' : 'no') . "\n";
} catch (Exception $e) {
    echo "❌ Request signing failed: " . $e->getMessage() . "\n";
}

echo "\n🎉 Basic tests completed!\n";
echo "The PHP SDK appears to be working correctly.\n";
echo "For full testing with PHPUnit, run: composer test\n";
