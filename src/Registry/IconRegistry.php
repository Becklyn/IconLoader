<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Data\IconNamespace;
use Becklyn\IconLoader\Exception\DuplicateNamespaceException;
use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Exception\InvalidIconKeyException;
use Becklyn\IconLoader\Exception\NamespaceMissingException;
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
     * Mapping from namespaces name to loader glob
     *
     * @var array
     */
    private $namespaces = [];


    /**
     * @var bool
     */
    private $isDebug;


    /**
     * @var string
     */
    private $projectDir;


    /**
     * @var string[][]|null
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
     * Registers a namespace with a project-relative path
     *
     * @param string $namespaceKey
     * @param string $relativePath
     */
    public function registerProjectNamespace (string $namespaceKey, string $relativePath, ?string $className = null) : void
    {
        $this->registerNamespace(
            new IconNamespace(
                $namespaceKey,
                "{$this->projectDir}/" . \ltrim($relativePath, "/"),
                $className
            )
        );
    }


    /**
     * Registers a namespace with a global path
     *
     * @param string $namespace
     * @param string $path
     * @param string $className
     */
    public function registerNamespace (IconNamespace $namespace) : void
    {
        $key = $namespace->getKey();

        if (\array_key_exists($key, $this->namespaces))
        {
            throw new DuplicateNamespaceException(\sprintf(
                "Duplicate namespace keys are not allowed: '%s'",
                $key
            ));
        }

        $this->namespaces[$key] = $namespace;
        // clear cache
        $this->registry = null;
    }


    /**
     * Fetches the registry.
     *
     * @return string[][]
     */
    private function fetchRegistry () : array
    {
        if (null === $this->registry)
        {
            $this->registry = !$this->isDebug
                ? $this->cache->get(self::CACHE_KEY, [$this, "loadAll"])
                : $this->loadAll();
        }

        return $this->registry;
    }


    /**
     * @return array
     */
    private function loadAll () : array
    {
        $registry = [];

        foreach ($this->namespaces as $namespace => $glob)
        {
            $registry[$namespace] = $this->loader->load($glob);
        }

        return $registry;
    }


    /**
     * Returns the icons' SVG content.
     *
     * @param string $key
     */
    public function get (string $key) : string
    {
        $parts = \explode("/", $key, 2);

        if (2 !== \count($parts))
        {
            throw new InvalidIconKeyException(\sprintf(
                "Invalid icon key. An icon key must have the format 'namespace/name', but '%s' given.",
                $key
            ));
        }

        $registry = $this->fetchRegistry();
        $namespaceRegistry = $registry[$parts[0]] ?? null;

        if (null === $namespaceRegistry)
        {
            if ($this->isDebug)
            {
                throw new NamespaceMissingException(\sprintf(
                    "Unknown icon namespace: '%s'",
                    $parts[0]
                ));
            }

            return "";
        }

        $icon = $namespaceRegistry[$parts[1]] ?? null;

        if (null === $icon)
        {
            if ($this->isDebug)
            {
                throw new IconMissingException($key);
            }

            return "";
        }

        return $icon;
    }
}
