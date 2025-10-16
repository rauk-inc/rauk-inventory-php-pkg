<?php

namespace RaukInventory\Tests;

use PHPUnit\Framework\TestCase;
use RaukInventory\RaukInventory;
use RaukInventory\Core\RaukInventoryClient;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationUpdateItem;
use RaukInventory\Types\OperationRequestOptions;
use RaukInventory\Types\OperationColor;
use RaukInventory\Types\OperationBrandDetails;
use RaukInventory\Types\OperationFactoryDetails;
use RaukInventory\Types\OperationEntities;
use RaukInventory\Types\OperationLocation;
use RaukInventory\Types\OperationDeleteResult;
use RaukInventory\Types\OperationUpdateResult;
use RaukInventory\Utils\RaukValidationError;
use RaukInventory\Utils\RaukAuthenticationError;
use RaukInventory\Utils\RaukNetworkError;

/**
 * Basic tests for RaukInventory PHP SDK
 */
class RaukInventoryTest extends TestCase
{
    private const TEST_CONFIG = [
        'apiKeyId' => 'test-key-id',
        'apiSecret' => 'test-secret',
        'apiPublicKey' => 'test-public-key',
        'apiBaseUrl' => 'https://test-api.example.com'
    ];

    protected function setUp(): void
    {
        // Reset singleton before each test
        RaukInventory::reset();
    }

    public function testCanCreateClient(): void
    {
        $client = new RaukInventoryClient(
            self::TEST_CONFIG['apiKeyId'],
            self::TEST_CONFIG['apiSecret'],
            self::TEST_CONFIG['apiPublicKey'],
            self::TEST_CONFIG['apiBaseUrl']
        );

        $this->assertInstanceOf(RaukInventoryClient::class, $client);
    }

    public function testCanInitializeRaukInventory(): void
    {
        $raukInventory = new RaukInventory(self::TEST_CONFIG);
        $this->assertInstanceOf(RaukInventory::class, $raukInventory);
    }

    public function testThrowsOnIncompleteConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('apiKeyId, apiSecret and apiPublicKey are required');

        new RaukInventory([
            'apiKeyId' => 'test',
            // Missing apiSecret and apiPublicKey
        ]);
    }

    public function testThrowsOnDoubleInitialization(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('RaukInventory is already initialized');

        new RaukInventory(self::TEST_CONFIG);
        new RaukInventory(self::TEST_CONFIG);
    }

    public function testCanCreateOperationObjects(): void
    {
        // Test creating operation objects
        $color = new OperationColor(id: 'color-123', name: 'Red');
        $brandDetails = new OperationBrandDetails(
            id: 'brand-101',
            name: 'Test Brand',
            type: 'luxury',
            subType: null
        );
        $factoryDetails = new OperationFactoryDetails(
            id: 'factory-789',
            name: 'Test Factory',
            type: 'manufacturing',
            subType: null
        );
        $entities = new OperationEntities(
            apiId: 'api-123',
            entityId: 'entity-456',
            factoryId: 'factory-789',
            brandId: 'brand-101'
        );
        $location = new OperationLocation(
            id: 'warehouse-1',
            name: 'Main Warehouse',
            details: null
        );

        $this->assertEquals('Red', $color->name);
        $this->assertEquals('luxury', $brandDetails->type);
        $this->assertEquals('factory-789', $entities->factoryId);
        $this->assertEquals('warehouse-1', $location->id);
    }

    public function testCanCreateQueryObject(): void
    {
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

        $this->assertEquals('ITEM-001', $query->sku);
    }

    public function testCanCreateUpdateItemObject(): void
    {
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
            set: ['packageQuantity' => 20]
        );

        $this->assertEquals(['packageQuantity' => 20], $updateItem->set);
    }

    public function testCanCreateRequestOptions(): void
    {
        $options = new OperationRequestOptions(
            select: ['sku' => 1, 'color' => 1],
            limit: 10,
            sort: ['createdAt' => -1],
            includeDeleted: false
        );

        $this->assertEquals(['sku' => 1, 'color' => 1], $options->select);
        $this->assertEquals(10, $options->limit);
        $this->assertEquals(['createdAt' => -1], $options->sort);
        $this->assertFalse($options->includeDeleted);
    }

    public function testCanConvertObjectsToArray(): void
    {
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
        $queryArray = $query->toArray();

        $this->assertIsArray($queryArray);
        $this->assertEquals('ITEM-001', $queryArray['sku']);
    }

    public function testCanCreateFromArray(): void
    {
        $queryData = [
            'sku' => 'ITEM-002',
            'color' => null,
            'deleted' => null,
            'entities' => null,
            'currentLocation' => null,
            'transitTo' => null,
            'brandDetails' => null,
            'factoryDetails' => null,
            'availability' => null,
            'packageQuantity' => null,
            'hardcode' => null,
            'id' => null
        ];
        $query = OperationQuery::fromArray($queryData);

        $this->assertEquals('ITEM-002', $query->sku);
    }

    public function testErrorClassesExist(): void
    {
        $this->assertTrue(class_exists(\RaukInventory\Utils\RaukError::class));
        $this->assertTrue(class_exists(\RaukInventory\Utils\RaukValidationError::class));
        $this->assertTrue(class_exists(\RaukInventory\Utils\RaukAuthenticationError::class));
        $this->assertTrue(class_exists(\RaukInventory\Utils\RaukNetworkError::class));
        $this->assertTrue(class_exists(\RaukInventory\Utils\RaukApiError::class));
    }

    public function testSignRequestFunctionExists(): void
    {
        $this->assertTrue(method_exists(\RaukInventory\Utils\SignRequest::class, 'sign'));
    }

    public function testGetClientMethod(): void
    {
        $raukInventory = new RaukInventory(self::TEST_CONFIG);
        $client = RaukInventory::getClient();

        $this->assertInstanceOf(RaukInventoryClient::class, $client);
    }

    public function testResetMethod(): void
    {
        $raukInventory = new RaukInventory(self::TEST_CONFIG);

        // Should work
        $this->assertInstanceOf(RaukInventory::class, $raukInventory);

        // Reset
        RaukInventory::reset();

        // Should be able to create new instance
        $newInstance = new RaukInventory(self::TEST_CONFIG);
        $this->assertInstanceOf(RaukInventory::class, $newInstance);
    }
}
