<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Registry;


use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Loader\IconLoader;

class IconRegistry
{
    /**
     * @var IconLoader
     */
    private $loader;


    /**
     * @var bool
     */
    private $throwOnMissing;


    /**
     * @var array|null
     */
    private $registry;


    /**
     * @param IconLoader $loader
     * @param bool       $throwOnMissing
     */
    public function __construct (IconLoader $loader, bool $isDebug)
    {
        $this->throwOnMissing = $isDebug;
        $this->loader = $loader;

        if (!$isDebug)
        {
            // load cache here
        }
    }


    /**
     * Returns the icons' SVG content
     *
     * @param string    $name
     * @param bool|null $ignoreMissing
     */
    public function get (string $name, ?bool $throwOnMissing = null) : ?string
    {
        if (null === $throwOnMissing)
        {
            $throwOnMissing = $this->throwOnMissing;
        }

        if (null === $this->registry)
        {
            $this->registry = $this->loader->load();
        }

        if (!isset($this->registry[$name]) && $throwOnMissing)
        {
            throw new IconMissingException($name);
        }

        return $this->registry[$name] ?? null;
    }
}
