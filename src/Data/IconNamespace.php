<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Data;

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
    private $className;


    /**
     * @param string $key
     * @param string $directory
     * @param string $className
     */
    public function __construct (string $key, string $directory, ?string $className)
    {
        $this->key = $key;
        $this->directory = $directory;
        $this->className = $className ?? "icon icon-%s";
    }


    /**
     * @return string
     */
    public function getKey () : string
    {
        return $this->key;
    }


    /**
     * @return string
     */
    public function getDirectory () : string
    {
        return $this->directory;
    }


    /**
     * @return string
     */
    public function getClassName () : string
    {
        return $this->className;
    }
}
