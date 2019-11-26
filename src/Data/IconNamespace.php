<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Data;

use Becklyn\IconLoader\Exception\InvalidNamespaceKeyException;

class IconNamespace
{
    /**
     * @var string
     */
    private $key;


    /**
     * @var string
     */
    private $directory;


    /**
     * @var string
     */
    private $classPattern;


    /**
     * @param string $classPattern
     */
    public function __construct (string $key, string $directory, ?string $classPattern = null)
    {
        if (false !== \strpos($key, "/"))
        {
            throw new InvalidNamespaceKeyException(\sprintf(
                "Invalid namespace key: '%s'",
                $key
            ));
        }

        $this->key = $key;
        $this->directory = $directory;
        $this->classPattern = $classPattern ?? "icon icon-%s";
    }


    /**
     */
    public function getKey () : string
    {
        return $this->key;
    }


    /**
     */
    public function getDirectory () : string
    {
        return $this->directory;
    }


    /**
     */
    public function getClassPattern () : string
    {
        return $this->classPattern;
    }
}
