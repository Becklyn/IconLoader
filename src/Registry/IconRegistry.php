<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Loader\IconLoader;
use Symfony\Contracts\Cache\CacheInterface;

class IconRegistry
{
    const CACHE_KEY = "becklyn.icon_loader.registry";

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var IconLoader
     */
    private $loader;


    /**
     * @var bool
     */
    private $isDebug;


    /**
     * @var array|null
     */
    private $registry;


    /**
     * @param CacheInterface $cache
     * @param IconLoader     $loader
     * @param bool           $isDebug
     */
    public function __construct (CacheInterface $cache, IconLoader $loader, bool $isDebug)
    {
        $this->cache = $cache;
        $this->loader = $loader;
        $this->isDebug = $isDebug;
    }


    /**
     * Fetches the registry.
     *
     * @return array
     */
    private function fetchRegistry () : array
    {
        if (null === $this->registry)
        {
            $this->registry = !$this->isDebug
                ? $this->cache->get(self::CACHE_KEY, [$this->loader, "load"])
                : $this->loader->load();
        }

        return $this->registry;
    }


    /**
     * Returns the icons' SVG content.
     *
     * @param string    $name
     * @param bool|null $throwOnMissing
     */
    public function get (string $name, ?bool $throwOnMissing = null) : ?string
    {
        if (null === $throwOnMissing)
        {
            $throwOnMissing = $this->isDebug;
        }

        $registry = $this->fetchRegistry();

        if (!isset($registry[$name]) && $throwOnMissing)
        {
            throw new IconMissingException($name);
        }

        return $registry[$name] ?? null;
    }
}
