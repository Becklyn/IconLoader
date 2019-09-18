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
    private $classPattern;


    /**
     * @param string $key
     * @param string $directory
     * @param string $classPattern
     */
    public function __construct (string $key, string $directory, ?string $classPattern = null)
    {
        $this->key = $key;
        $this->directory = $directory;
        $this->classPattern = $classPattern ?? "icon icon-%s";
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
    public function getClassPattern () : string
    {
        return $this->classPattern;
    }
}
