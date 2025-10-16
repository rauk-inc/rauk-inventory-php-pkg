<?php

namespace RaukInventory\Core;

use RaukInventory\Types\InventoryItem;
use RaukInventory\Types\OperationCreateItem;
use RaukInventory\Types\OperationUpdateItem;
use RaukInventory\Types\OperationQuery;
use RaukInventory\Types\OperationAggregatePipeline;
use RaukInventory\Types\OperationRequestOptions;
use RaukInventory\Types\OperationDeleteResult;
use RaukInventory\Types\OperationUpdateResult;
use RaukInventory\Types\OperationInsertResult;
use RaukInventory\Utils\SignRequest;
use RaukInventory\Utils\RaukError;
use RaukInventory\Utils\RaukValidationError;
use RaukInventory\Utils\RaukAuthenticationError;
use RaukInventory\Utils\RaukNetworkError;
use RaukInventory\Utils\RaukApiError;

/**
 * Core client for Rauk Inventory API
 */
readonly class RaukInventoryClient
{
    public function __construct(
        private string $apiKeyId,
        private string $apiSecret,
        private string $apiPublicKey,
        private string $apiBaseUrl = 'https://inventory.rauk.app',
    ) {
        if (empty($apiKeyId) || empty($apiSecret) || empty($apiPublicKey)) {
            throw new \InvalidArgumentException('apiKeyId, apiSecret and apiPublicKey are required');
        }
    }

    /**
     * Make authenticated request to API
     */
    private function request(array $requestArray): array
    {
        $signature = SignRequest::sign(
            $this->apiKeyId,
            $this->apiSecret,
            $this->apiPublicKey,
            $requestArray
        );

        $payload = json_encode($requestArray);
        if ($payload === false) {
            throw new \RuntimeException('Failed to encode request payload');
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => "{$this->apiBaseUrl}/query",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Rai-Signature: ' . $signature,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);

        curl_close($ch);

        if ($curlErrno !== 0) {
            // Network error
            throw new RaukNetworkError(
                'Network request failed - check your internet connection and API endpoint',
                null,
                null
            );
        }

        if ($httpCode >= 400) {
            $errorBody = json_decode($response, true) ?? [];
            throw \RaukInventory\Utils\parseApiError($httpCode, $errorBody);
        }

        $data = json_decode($response, true);
        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new RaukApiError('Invalid JSON response from API', null, null);
        }

        return $data;
    }

    /**
     * Create a new inventory item
     */
    public function create(OperationCreateItem $item, ?OperationRequestOptions $options = null): InventoryItem
    {
        $requestArray = $options !== null
            ? ['insertOne', $item->toArray(), $options->toArray()]
            : ['insertOne', $item->toArray()];

        $response = $this->request($requestArray);
        return InventoryItem::fromArray($response);
    }

    /**
     * Find multiple inventory items
     */
    public function find(OperationQuery $query, ?OperationRequestOptions $options = null): array
    {
        $requestArray = $options !== null
            ? ['find', $query->toArray(), $options->toArray()]
            : ['find', $query->toArray()];

        $response = $this->request($requestArray);
        return array_map(
            fn($item) => InventoryItem::fromArray($item),
            $response
        );
    }

    /**
     * Find a single inventory item
     */
    public function findOne(OperationQuery $query, ?OperationRequestOptions $options = null): ?InventoryItem
    {
        $results = $this->find($query, new OperationRequestOptions(
            select: $options?->select,
            limit: 1,
            sort: $options?->sort,
            includeDeleted: $options?->includeDeleted
        ));

        return $results[0] ?? null;
    }

    /**
     * Update inventory items
     */
    public function update(
        OperationQuery $query,
        OperationUpdateItem $update,
        ?OperationRequestOptions $options = null
    ): OperationUpdateResult {
        $requestArray = $options !== null
            ? ['findOneAndUpdate', $query->toArray(), $update->toArray(), $options->toArray()]
            : ['findOneAndUpdate', $query->toArray(), $update->toArray()];

        $response = $this->request($requestArray);
        return OperationUpdateResult::fromArray($response);
    }

    /**
     * Delete inventory items (marks as deleted, doesn't remove)
     */
    public function delete(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        $requestArray = $options !== null
            ? ['findOneAndUpdate', $query->toArray(), ['deleted' => ['status' => true]], $options->toArray()]
            : ['findOneAndUpdate', $query->toArray(), ['deleted' => ['status' => true]]];

        $response = $this->request($requestArray);
        return OperationDeleteResult::fromArray($response);
    }

    /**
     * Perform aggregation operations
     */
    public function aggregate(OperationAggregatePipeline $pipeline, ?OperationRequestOptions $options = null): array
    {
        $requestArray = $options !== null
            ? ['aggregate', array_map(fn($stage) => $stage->toArray(), $pipeline), $options->toArray()]
            : ['aggregate', array_map(fn($stage) => $stage->toArray(), $pipeline)];

        return $this->request($requestArray);
    }

    /**
     * Bulk write operations
     */
    public function bulkWrite(array $operations, ?OperationRequestOptions $options = null): array
    {
        $requestArray = $options !== null
            ? ['bulkWrite', array_map(fn($op) => $op->toArray(), $operations), $options->toArray()]
            : ['bulkWrite', array_map(fn($op) => $op->toArray(), $operations)];

        return $this->request($requestArray);
    }

    /**
     * Update multiple inventory items
     */
    public function updateMany(
        OperationQuery $query,
        OperationUpdateItem $update,
        ?OperationRequestOptions $options = null
    ): OperationUpdateResult {
        $requestArray = $options !== null
            ? ['updateMany', $query->toArray(), $update->toArray(), $options->toArray()]
            : ['updateMany', $query->toArray(), $update->toArray()];

        $response = $this->request($requestArray);
        return OperationUpdateResult::fromArray($response);
    }

    /**
     * Delete a single inventory item
     */
    public function deleteOne(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        $requestArray = $options !== null
            ? ['deleteOne', $query->toArray(), $options->toArray()]
            : ['deleteOne', $query->toArray()];

        $response = $this->request($requestArray);
        return OperationDeleteResult::fromArray($response);
    }

    /**
     * Delete multiple inventory items
     */
    public function deleteMany(OperationQuery $query, ?OperationRequestOptions $options = null): OperationDeleteResult
    {
        $requestArray = $options !== null
            ? ['deleteMany', $query->toArray(), $options->toArray()]
            : ['deleteMany', $query->toArray()];

        $response = $this->request($requestArray);
        return OperationDeleteResult::fromArray($response);
    }

    /**
     * Batch update multiple items with a simplified interface
     */
    public function updateBatch(array $updates, ?OperationRequestOptions $options = null): array
    {
        $bulkOperations = array_map(
            fn($update) => new class($update[0], $update[1]) {
                public function __construct(
                    private OperationQuery $filter,
                    private OperationUpdateItem $update
                ) {}

                public function toArray(): array
                {
                    return [
                        'updateOne' => [
                            'filter' => $this->filter->toArray(),
                            'update' => $this->update->toArray(),
                        ]
                    ];
                }
            },
            $updates
        );

        return $this->bulkWrite($bulkOperations, $options);
    }
}
