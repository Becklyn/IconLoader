<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Exception;

class IconMissingException extends IconLoaderException
{
    /**
     * @inheritDoc
     */
    public function __construct (string $iconName, ?\Throwable $previous = null)
    {
        parent::__construct(
            \sprintf("Could not find icon: '%s'", $iconName),
            $previous
        );
    }
}
