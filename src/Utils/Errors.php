<?php

namespace RaukInventory\Utils;

/**
 * Validation error detail information
 */
readonly class ValidationErrorDetail
{
    public function __construct(
        public string $property,
        /** @var string[] */
        public array $constraints,
        /** @var ValidationErrorDetail[] */
        public array $children,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            property: $data['property'],
            constraints: $data['constraints'] ?? [],
            children: array_map(
                fn($child) => ValidationErrorDetail::fromArray($child),
                $data['children'] ?? []
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'property' => $this->property,
            'constraints' => $this->constraints,
            'children' => array_map(
                fn(ValidationErrorDetail $child) => $child->toArray(),
                $this->children
            ),
        ];
    }

    /**
     * Get all validation error messages flattened
     */
    public function getAllMessages(): array
    {
        $messages = $this->constraints;

        foreach ($this->children as $child) {
            $messages = array_merge($messages, $child->getAllMessages());
        }

        return $messages;
    }

    /**
     * Get validation errors by property path
     */
    public function getErrorsForProperty(string $propertyPath): array
    {
        $errors = [];

        if ($this->property === $propertyPath) {
            $errors[] = $this;
        }

        foreach ($this->children as $child) {
            $errors = array_merge($errors, $child->getErrorsForProperty($propertyPath));
        }

        return $errors;
    }
}

/**
 * API error response structure
 */
readonly class RaukApiErrorResponse
{
    public function __construct(
        public bool $success,
        public array $error,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            error: $data['error'] ?? [],
        );
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error,
        ];
    }
}

/**
 * Error options for Rauk errors
 */
readonly class RaukErrorOptions
{
    public function __construct(
        public ?int $statusCode,
        public ?string $requestId,
        public ?string $timestamp,
        public ?array $context,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            statusCode: $data['statusCode'] ?? null,
            requestId: $data['requestId'] ?? null,
            timestamp: $data['timestamp'] ?? null,
            context: $data['context'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'statusCode' => $this->statusCode,
            'requestId' => $this->requestId,
            'timestamp' => $this->timestamp,
            'context' => $this->context,
        ], fn($value) => $value !== null);
    }
}

/**
 * Base class for all Rauk SDK errors
 */
class RaukError extends \Exception
{
    public readonly ?int $statusCode;
    public readonly ?string $requestId;
    public readonly ?string $timestamp;
    public readonly ?array $context;
    public readonly ?RaukApiErrorResponse $originalError;

    public function __construct(
        string $message,
        ?RaukErrorOptions $options = null,
        ?RaukApiErrorResponse $originalError = null
    ) {
        parent::__construct($message);

        $this->statusCode = $options?->statusCode;
        $this->requestId = $options?->requestId;
        $this->timestamp = $options?->timestamp ?? date('c');
        $this->context = $options?->context;
        $this->originalError = $originalError;
    }

    /**
     * Convert to a plain array for serialization
     */
    public function toArray(): array
    {
        return [
            'name' => static::class,
            'message' => $this->message,
            'statusCode' => $this->statusCode,
            'requestId' => $this->requestId,
            'timestamp' => $this->timestamp,
            'context' => $this->context,
            'stack' => $this->getTraceAsString(),
            'originalError' => $this->originalError?->toArray(),
        ];
    }

    /**
     * Convert to JSON for serialization
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}

/**
 * Validation errors from API validation rules
 */
class RaukValidationError extends RaukError
{
    /** @var ValidationErrorDetail[] */
    public readonly array $validationErrors;

    public function __construct(
        string $message,
        array $validationErrors,
        ?RaukErrorOptions $options = null,
        ?RaukApiErrorResponse $originalError = null
    ) {
        parent::__construct($message, $options, $originalError);
        $this->validationErrors = $validationErrors;
    }

    /**
     * Get all validation error messages flattened
     */
    public function getAllMessages(): array
    {
        $messages = [];

        foreach ($this->validationErrors as $error) {
            $messages = array_merge($messages, $error->getAllMessages());
        }

        return $messages;
    }

    /**
     * Get validation errors by property path
     */
    public function getErrorsForProperty(string $propertyPath): array
    {
        $errors = [];

        foreach ($this->validationErrors as $error) {
            $errors = array_merge($errors, $error->getErrorsForProperty($propertyPath));
        }

        return $errors;
    }
}

/**
 * Authentication/Authorization errors
 */
class RaukAuthenticationError extends RaukError
{
    public function __construct(
        string $message = 'Authentication failed',
        ?RaukErrorOptions $options = null,
        ?RaukApiErrorResponse $originalError = null
    ) {
        parent::__construct($message, $options, $originalError);
    }
}

/**
 * Network/Connection errors
 */
class RaukNetworkError extends RaukError
{
    public function __construct(
        string $message = 'Network request failed',
        ?RaukErrorOptions $options = null,
        ?RaukApiErrorResponse $originalError = null
    ) {
        parent::__construct($message, $options, $originalError);
    }
}

/**
 * Generic API errors
 */
class RaukApiError extends RaukError
{
    public function __construct(
        string $message,
        ?RaukErrorOptions $options = null,
        ?RaukApiErrorResponse $originalError = null
    ) {
        parent::__construct($message, $options, $originalError);
    }
}

/**
 * Parse API error response into appropriate error type
 */
function parseApiError(int $statusCode, array $errorBody): RaukError
{
    $errorOptions = new RaukErrorOptions(
        statusCode: $statusCode,
        timestamp: date('c'),
        context: null,
        requestId: null
    );

    $apiError = isset($errorBody['error']) ? RaukApiErrorResponse::fromArray($errorBody) : null;

    // Handle validation errors with detailed structure
    if (isset($errorBody['error']['errors']) && is_array($errorBody['error']['errors'])) {
        $validationErrors = array_map(
            fn($error) => ValidationErrorDetail::fromArray($error),
            $errorBody['error']['errors']
        );

        $allMessages = [];
        foreach ($validationErrors as $error) {
            $allMessages = array_merge($allMessages, $error->getAllMessages());
        }

        return new RaukValidationError(
            $errorBody['error']['message'] ?? implode('; ', $allMessages),
            $validationErrors,
            $errorOptions,
            $apiError
        );
    }

    // Handle authentication errors
    if ($statusCode === 401 || $statusCode === 403) {
        return new RaukAuthenticationError(
            $errorBody['error']['message'] ?? 'Authentication failed',
            $errorOptions,
            $apiError
        );
    }

    // Handle network errors
    if ($statusCode >= 500) {
        return new RaukNetworkError(
            $errorBody['error']['message'] ?? 'Server error occurred',
            $errorOptions,
            $apiError
        );
    }

    // Generic API error
    return new RaukApiError(
        $errorBody['error']['message'] ?? "API request failed with status {$statusCode}",
        $errorOptions,
        $apiError
    );
}

/**
 * Type guard to check if an error is a Rauk SDK error
 */
function isRaukError(\Throwable $error): bool
{
    return $error instanceof RaukError;
}

/**
 * Type guard to check if an error is a validation error
 */
function isValidationError(\Throwable $error): bool
{
    return $error instanceof RaukValidationError;
}

/**
 * Type guard to check if an error is an authentication error
 */
function isAuthenticationError(\Throwable $error): bool
{
    return $error instanceof RaukAuthenticationError;
}

/**
 * Type guard to check if an error is a network error
 */
function isNetworkError(\Throwable $error): bool
{
    return $error instanceof RaukNetworkError;
}
