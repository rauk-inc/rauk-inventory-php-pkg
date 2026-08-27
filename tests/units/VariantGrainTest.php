<?php

namespace RaukInventory\Tests;

use PHPUnit\Framework\TestCase;
use RaukInventory\Types\InventoryItem;
use RaukInventory\Types\Variant;
use RaukInventory\Types\StatusDetails;
use RaukInventory\Types\OperationVariant;
use RaukInventory\Types\OperationQueryVariant;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationUpdateItem;
use RaukInventory\Types\OperationBaseItem;
use RaukInventory\Types\OperationBrandDetails;
use RaukInventory\Types\OperationFactoryDetails;
use RaukInventory\Types\OperationEntities;
use RaukInventory\Types\OperationLocation;
use RaukInventory\Types\OperationAvailability;
use RaukInventory\Types\OperationStatusDetails;

/**
 * RAUK-442: color → variant hard-cut; qty on variant grain; serials/hardcode opt-in.
 */
class VariantGrainTest extends TestCase
{
    private function baseCreatePayload(array $overrides = []): array
    {
        return array_merge([
            'entities' => [
                'apiId' => 'api-123',
                'entityId' => 'entity-456',
                'factoryId' => 'factory-789',
                'brandId' => 'brand-101',
            ],
            'currLoc' => [
                'id' => 'warehouse-1',
                'name' => 'Main Warehouse',
            ],
            'sku' => 'ITEM-001',
            'qty' => 10,
            'variant' => [
                'id' => 'variant-123',
                'name' => 'Traffic Red',
            ],
            'brandDetails' => [
                'id' => 'brand-101',
                'name' => 'Premium Brand',
                'type' => 'luxury',
            ],
            'factoryDetails' => [
                'id' => 'factory-789',
                'name' => 'Main Factory',
                'type' => 'manufacturing',
            ],
        ], $overrides);
    }

    public function testCreateAcceptsVariantNotColor(): void
    {
        $item = OperationCreateItem::fromArray($this->baseCreatePayload());

        $this->assertInstanceOf(OperationVariant::class, $item->variant);
        $this->assertEquals('Traffic Red', $item->variant->name);
        $this->assertEquals('variant-123', $item->variant->id);
        $this->assertEquals(10, $item->qty);

        $array = $item->toArray();
        $this->assertArrayHasKey('variant', $array);
        $this->assertArrayNotHasKey('color', $array);
        $this->assertEquals(['id' => 'variant-123', 'name' => 'Traffic Red'], $array['variant']);
    }

    public function testCreateDoesNotAliasColorToVariant(): void
    {
        $payload = $this->baseCreatePayload();
        unset($payload['variant']);
        $payload['color'] = ['name' => 'Black'];

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('variant is required for create operation');

        OperationCreateItem::fromArray($payload);
    }

    public function testBaseItemFromArrayIgnoresColorKey(): void
    {
        $base = OperationBaseItem::fromArray([
            'sku' => 'ITEM-001',
            'qty' => 2,
            'color' => ['name' => 'Black'],
        ]);

        $this->assertNull($base->variant);
        $array = $base->toArray();
        $this->assertArrayNotHasKey('color', $array);
        $this->assertArrayNotHasKey('variant', $array);
    }

    public function testQueryUsesVariantNotColor(): void
    {
        $query = OperationQuery::fromArray([
            'variant' => ['name' => 'Blue'],
            'sku' => 'ITEM-001',
        ]);

        $this->assertInstanceOf(OperationQueryVariant::class, $query->variant);
        $this->assertEquals('Blue', $query->variant->name);

        $array = $query->toArray();
        $this->assertArrayHasKey('variant', $array);
        $this->assertArrayNotHasKey('color', $array);
        $this->assertEquals(['name' => 'Blue'], $array['variant']);
    }

    public function testUpdateUsesVariantNotColor(): void
    {
        $update = OperationUpdateItem::fromArray([
            'variant' => ['name' => 'Ocean Blue'],
        ]);

        $this->assertEquals('Ocean Blue', $update->variant->name);
        $array = $update->toArray();
        $this->assertArrayHasKey('variant', $array);
        $this->assertArrayNotHasKey('color', $array);
    }

    public function testGenericQtyRowWithoutHardcode(): void
    {
        $item = OperationCreateItem::fromArray($this->baseCreatePayload([
            'qty' => 25,
            'variant' => ['name' => 'Black'],
        ]));

        $this->assertEquals(25, $item->qty);
        $this->assertEquals('Black', $item->variant->name);
        $this->assertNull($item->hardcode);

        $array = $item->toArray();
        $this->assertArrayNotHasKey('hardcode', $array);
    }

    public function testHardcodeOptInForUniqueItemTracking(): void
    {
        $item = OperationCreateItem::fromArray($this->baseCreatePayload([
            'qty' => 1,
            'variant' => ['name' => 'Black'],
            'hardcode' => 'SN-ABC-001',
        ]));

        $this->assertEquals('SN-ABC-001', $item->hardcode);
        $this->assertEquals(1, $item->qty);
        $this->assertEquals('Black', $item->variant->name);
    }

    public function testAvailabilityReservedQtyWithoutRowSplit(): void
    {
        $item = OperationCreateItem::fromArray($this->baseCreatePayload([
            'qty' => 10,
            'variant' => ['name' => 'Black'],
            'availability' => [
                'produced' => ['orderId' => null],
                'reserved' => ['orderId' => 'ORD-42', 'qty' => 3],
                'sold' => ['orderId' => null],
            ],
        ]));

        $this->assertEquals(10, $item->qty);
        $this->assertEquals('Black', $item->variant->name);
        $this->assertInstanceOf(OperationAvailability::class, $item->availability);
        $this->assertInstanceOf(OperationStatusDetails::class, $item->availability->reserved);
        $this->assertEquals('ORD-42', $item->availability->reserved->orderId);
        $this->assertEquals(3, $item->availability->reserved->qty);

        $array = $item->toArray();
        $this->assertEquals(3, $array['availability']['reserved']['qty']);
        $this->assertArrayNotHasKey('color', $array);
    }

    public function testInventoryItemRoundTripUsesVariant(): void
    {
        $data = [
            'hardcode' => null,
            'entities' => [
                'apiId' => 'api-123',
                'entityId' => 'entity-456',
                'factoryId' => 'factory-789',
                'brandId' => 'brand-101',
            ],
            'currLoc' => ['id' => 'warehouse-1', 'name' => 'Main Warehouse'],
            'availability' => [
                'reserved' => ['orderId' => 'ORD-1', 'qty' => 2],
            ],
            'sku' => 'ITEM-001',
            'brandDetails' => ['id' => 'brand-101', 'name' => 'Brand', 'type' => 'type'],
            'qty' => 10,
            'variant' => ['id' => 'v-1', 'name' => 'Red'],
            'factoryDetails' => ['id' => 'factory-789', 'name' => 'Factory', 'type' => 'type'],
            'deleted' => ['status' => false],
            'locationHistory' => [],
            'id' => 'item-1',
        ];

        $item = InventoryItem::fromArray($data);

        $this->assertInstanceOf(Variant::class, $item->variant);
        $this->assertEquals('Red', $item->variant->name);
        $this->assertEquals(10, $item->qty);
        $this->assertNull($item->hardcode);

        $this->assertArrayHasKey(0, $item->availability);
        $this->assertInstanceOf(StatusDetails::class, $item->availability[0]);
        $this->assertEquals(2, $item->availability[0]->qty);

        $array = $item->toArray();
        $this->assertArrayHasKey('variant', $array);
        $this->assertArrayNotHasKey('color', $array);
        $this->assertEquals('Red', $array['variant']['name']);
    }

    public function testInventoryItemRequiresVariantKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('variant is required');

        InventoryItem::fromArray([
            'entities' => [
                'apiId' => 'a',
                'entityId' => 'e',
                'factoryId' => 'f',
                'brandId' => 'b',
            ],
            'currLoc' => ['id' => 'w'],
            'availability' => [],
            'sku' => 'SKU',
            'qty' => 1,
            'color' => ['name' => 'Red'],
            'deleted' => ['status' => false],
            'id' => '1',
        ]);
    }

    public function testOperationVariantClassExistsAndColorDoesNot(): void
    {
        $this->assertTrue(class_exists(OperationVariant::class));
        $this->assertTrue(class_exists(OperationQueryVariant::class));
        $this->assertTrue(class_exists(Variant::class));
        $this->assertFalse(class_exists(\RaukInventory\Types\OperationColor::class));
        $this->assertFalse(class_exists(\RaukInventory\Types\Color::class));
        $this->assertFalse(class_exists(\RaukInventory\Types\OperationQueryColor::class));
    }
}
