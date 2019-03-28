<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Loader\IconLoader;
use Becklyn\IconLoader\Registry\IconRegistry;
use PHPUnit\Framework\TestCase;
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
        $cache = $this->getMockBuilder(CacheInterface::class)
            ->getMock();

        $cache->method("get")
            ->willReturn($map);

        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $loader->method("load")
            ->willReturn($map);

        return new IconRegistry($cache, $loader, $isDebug);
    }


    /**
     *
     */
    public function testFetch () : void
    {
        $registry = $this->buildRegistry(["a" => "1", "b" => "2"], true);

        static::assertSame("1", $registry->get("a"));
        static::assertSame("2", $registry->get("b"));
    }


    /**
     *
     */
    public function testExceptionOnMissingInDebug () : void
    {
        $this->expectException(IconMissingException::class);
        $this->expectExceptionMessage("Could not find icon: 'missing'.");

        $registry = $this->buildRegistry([], true);
        $registry->get("missing");
    }


    /**
     *
     */
    public function testIgnoreOnMissingInProd () : void
    {
        $registry = $this->buildRegistry([], false);
        static::assertSame("", $registry->get("missing"));
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
            ->willReturn(["a" => "a"]);

        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $registry = new IconRegistry($cache, $loader, false);
        static::assertSame("a", $registry->get("a"));
    }
}
