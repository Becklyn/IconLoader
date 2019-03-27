<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Exception;

class IconConflictException extends IconLoaderException
{
    /**
     * @inheritDoc
     */
    public function __construct (string $name, ?\Throwable $previous = null)
    {
        $message = \sprintf(
            "Conflict for icon '%s'. Multiple icons with the same name but different content found.",
            $name
        );

        parent::__construct($message, $previous);
    }

}
