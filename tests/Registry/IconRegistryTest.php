<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Data\IconNamespace;
use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Exception\NamespaceMissingException;
use Becklyn\IconLoader\Loader\IconLoader;
use Becklyn\IconLoader\Registry\IconRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class IconRegistryTest extends TestCase
{
    /**
     * @param array $map
     *
     * @return IconRegistry
     */
    private function buildRegistry (array $map, bool $isDebug)
    {
        $cache = new ArrayAdapter();

        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $loader->method("load")
            ->willReturnCallback(function ($key) use ($map) { return $map[$key] ?? []; });

        $registry = new IconRegistry($cache, $loader, $isDebug);

        // register namespaces
        foreach ($map as $namespace => $config)
        {
            $registry->registerNamespace(new IconNamespace($namespace, $namespace));
        }

        return $registry;
    }


    /**
     *
     */
    public function testFetch () : void
    {
        $registry = $this->buildRegistry(["test" => ["a" => "1", "b" => "2"]], true);

        static::assertSame("1", $registry->get("test/a"));
        static::assertSame("2", $registry->get("test/b"));
    }


    /**
     *
     */
    public function testExceptionOnMissingIconInDebug () : void
    {
        $this->expectException(IconMissingException::class);
        $this->expectExceptionMessage("Could not find icon 'missing' in namespace 'test'.");

        $registry = $this->buildRegistry(["test" => []], true);
        $registry->get("test/missing");
    }


    /**
     *
     */
    public function testIgnoreExceptionOnMissingIconInProd () : void
    {
        $registry = $this->buildRegistry([], false);
        static::assertSame("", $registry->get("missing/icon"));
    }



    /**
     *
     */
    public function testExceptionOnMissingNamespaceInDebug () : void
    {
        $this->expectException(NamespaceMissingException::class);
        $this->expectExceptionMessage("Unknown icon namespace: 'missing'.");

        $registry = $this->buildRegistry([], true);
        $registry->get("missing/icon");
    }


    /**
     *
     */
    public function testIgnoreExceptionOnMissingNamespaceInProd () : void
    {
        $registry = $this->buildRegistry([], false);
        static::assertSame("", $registry->get("test/missing"));
    }


    /**
     *
     */
    public function testCacheFetchOnProd () : void
    {
        $cache = $this->getMockBuilder(CacheInterface::class)
            ->getMock();

        $cache->expects(static::once())
            ->method("get")
            ->willReturn(["test" => ["a" => "a"]]);

        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $registry = new IconRegistry($cache, $loader, false);
        static::assertSame("a", $registry->get("test/a"));
    }
}
