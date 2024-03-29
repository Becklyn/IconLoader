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
    public const CACHE_KEY = "becklyn.icon_loader.registry";

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var IconLoader
     */
    private $loader;


    /**
     * @var IconNamespace[]
     */
    private $namespaces = [];


    /**
     * @var string
     */
    private $projectDir;


    /**
     * @var bool
     */
    private $isDebug;


    /**
     * @var string[][]|null
     */
    private $registry;


    /**
     */
    public function __construct (CacheInterface $cache, IconLoader $loader, string $projectDir, bool $isDebug)
    {
        $this->cache = $cache;
        $this->loader = $loader;
        $this->projectDir = \rtrim($projectDir, "/");
        $this->isDebug = $isDebug;
    }


    /**
     * Registers a namespace with a project-relative path.
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
     * Registers a namespace with a global path.
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
                ? $this->cache->get(self::CACHE_KEY, function () { return $this->loadAll(); })
                : $this->loadAll();
        }

        return $this->registry;
    }


    /**
     */
    private function loadAll () : array
    {
        $registry = [];

        foreach ($this->namespaces as $namespace)
        {
            $registry[$namespace->getKey()] = $this->loader->load(
                $namespace->getDirectory(),
                $namespace->getClassPattern()
            );
        }

        return $registry;
    }


    /**
     * Returns the icons' SVG content.
     */
    public function get (string $key) : string
    {
        $parts = \explode("/", $key);

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
                throw new NamespaceMissingException($parts[0]);
            }

            return "";
        }

        $icon = $namespaceRegistry[$parts[1]] ?? null;

        if (null === $icon)
        {
            if ($this->isDebug)
            {
                throw new IconMissingException($parts[1], $parts[0]);
            }

            return "";
        }

        return $icon;
    }


    /**
     * Returns all registered namespace keys.
     *
     * @return string[]
     */
    public function getAllNamespaceKeys () : array
    {
        $keys = \array_keys($this->namespaces);
        \natcasesort($keys);
        return $keys;
    }


    /**
     * Returns the list of all registered icons in the given namespace.
     *
     * @return string[]
     */
    public function getIconsInNamespace (string $namespaceKey) : array
    {
        $registry = $this->fetchRegistry();
        $icons = \array_keys($registry[$namespaceKey] ?? []);
        \natcasesort($icons);
        return $icons;
    }
}
