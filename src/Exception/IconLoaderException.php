<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Exception;

class IconLoaderException extends \RuntimeException
{
    /**
     * @inheritDoc
     */
    public function __construct (string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
