<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Exception;

class NamespaceMissingException extends IconLoaderException
{
    /**
     * @inheritDoc
     */
    public function __construct (string $namespaceKey, ?\Throwable $previous = null)
    {
        parent::__construct(
            \sprintf("Unknown icon namespace: '%s'.", $namespaceKey),
            $previous
        );
    }
}
