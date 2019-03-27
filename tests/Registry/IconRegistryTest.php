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
    public function testFetch ()
    {
        $registry = $this->buildRegistry(["a" => "1", "b" => "2"], true);

        self::assertSame("1", $registry->get("a"));
        self::assertSame("2", $registry->get("b"));
        self::assertSame("", $registry->get("missing"));
    }


    /**
     *
     */
    public function testExceptionOnMissing ()
    {
        $this->expectException(IconMissingException::class);
        $this->expectExceptionMessage("Could not find icon: 'missing'.");

        $registry = $this->buildRegistry([], false);
        $registry->get("missing");
    }


    /**
     *
     */
    public function testCacheFetchOnProd ()
    {
        $cache = $this->getMockBuilder(CacheInterface::class)
            ->getMock();

        $cache->expects(self::once())
            ->method("get")
            ->willReturn(["a" => "a"]);

        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $registry = new IconRegistry($cache, $loader, false);
        self::assertSame("a", $registry->get("a"));
    }
}
