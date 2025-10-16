<?php

namespace RaukInventory;

use RaukInventory\Core\RaukInventoryClient;
use RaukInventory\Types\InventoryItem;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationUpdateItem;
use RaukInventory\Types\OperationAggregatePipeline;
use RaukInventory\Types\OperationRequestOptions;
use RaukInventory\Types\OperationDeleteResult;
use RaukInventory\Types\OperationUpdateResult;
use RaukInventory\Types\OperationInsertResult;

/**
 * Main RaukInventory class with static methods and singleton pattern
 */
class RaukInventory
{
    private static ?RaukInventory $instance = null;
    private RaukInventoryClient $client;

    /**
     * Constructor for RaukInventory
     */
    public function __construct(array $config)
    {
        if (isset($config['apiKeyId'], $config['apiSecret'], $config['apiPublicKey'])) {
            $this->client = new RaukInventoryClient(
                $config['apiKeyId'],
                $config['apiSecret'],
                $config['apiPublicKey'],
                $config['apiBaseUrl'] ?? 'https://inventory.rauk.app'
            );
        } else {
            throw new \InvalidArgumentException('apiKeyId, apiSecret and apiPublicKey are required');
        }

        if (self::$instance !== null) {
            throw new \RuntimeException('RaukInventory is already initialized. Use the existing instance.');
        }

        self::$instance = $this;
    }

    /**
     * Create a new inventory item
     */
    public static function create(OperationCreateItem $item, ?OperationRequestOptions $options = null): InventoryItem
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->create($item, $options);
    }

    /**
     * Find multiple inventory items
     */
    public static function find(OperationQuery $query, ?OperationRequestOptions $options = null): array
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->find($query, $options);
    }

    /**
     * Find a single inventory item
     */
    public static function findOne(OperationQuery $query, ?OperationRequestOptions $options = null): ?InventoryItem
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->findOne($query, $options);
    }

    /**
     * Update inventory items
     */
    public static function update(
        OperationQuery $query,
        OperationUpdateItem $update,
        ?OperationRequestOptions $options = null
    ): OperationUpdateResult {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->update($query, $update, $options);
    }

    /**
     * Delete inventory items (marks as deleted, doesn't remove)
     */
    public static function delete(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->delete($query, $options);
    }

    /**
     * Perform aggregation operations
     */
    public static function aggregate(OperationAggregatePipeline $pipeline, ?OperationRequestOptions $options = null): array
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->aggregate($pipeline, $options);
    }

    /**
     * Bulk write operations
     */
    public static function bulkWrite(array $operations, ?OperationRequestOptions $options = null): array
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->bulkWrite($operations, $options);
    }

    /**
     * Update multiple inventory items
     */
    public static function updateMany(
        OperationQuery $query,
        OperationUpdateItem $update,
        ?OperationRequestOptions $options = null
    ): OperationUpdateResult {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->updateMany($query, $update, $options);
    }

    /**
     * Delete a single inventory item
     */
    public static function deleteOne(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->deleteOne($query, $options);
    }

    /**
     * Delete multiple inventory items
     */
    public static function deleteMany(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->deleteMany($query, $options);
    }

    /**
     * Batch update multiple items with a simplified interface
     */
    public static function updateBatch(array $updates, ?OperationRequestOptions $options = null): array
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized with "new RaukInventory(config)" before calling static methods.');
        }
        return self::$instance->client->updateBatch($updates, $options);
    }

    /**
     * Get the underlying client instance for direct access
     */
    public static function getClient(): RaukInventoryClient
    {
        if (self::$instance === null) {
            throw new \RuntimeException('RaukInventory must be initialized before accessing the client.');
        }
        return self::$instance->client;
    }

    /**
     * Reset the singleton instance (mainly for testing)
     */
    public static function reset(): void
    {
        self::$instance = null;
    }
}
