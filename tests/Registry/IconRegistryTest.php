<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Data\IconNamespace;
use Becklyn\IconLoader\Exception\DuplicateNamespaceException;
use Becklyn\IconLoader\Exception\IconMissingException;
use Becklyn\IconLoader\Exception\NamespaceMissingException;
use Becklyn\IconLoader\Loader\IconLoader;
use Becklyn\IconLoader\Registry\IconRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Contracts\Cache\CacheInterface;

class IconRegistryTest extends TestCase
{

    private $fixtures;


    /**
     * @inheritDoc
     */
    protected function setUp () : void
    {
        $this->fixtures = \dirname(__DIR__) . "/_fixtures";
    }


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

        $registry = new IconRegistry($cache, $loader, "/project/dir/", $isDebug);

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

        $registry = new IconRegistry($cache, $loader, "/project/dir/", false);
        static::assertSame("a", $registry->get("test/a"));
    }


    /**
     * @return array
     */
    public function provideDebugAndProd () : array
    {
        return [[true], [false]];
    }


    /**
     * @dataProvider provideDebugAndProd
     *
     * @param bool $debug
     */
    public function testRegularUsage (bool $debug) : void
    {
        $registry = new IconRegistry(new ArrayAdapter(), new IconLoader(), "/project/dir/", $debug);
        $registry->registerNamespace(new IconNamespace("a", "{$this->fixtures}/valid/a"));
        $registry->registerNamespace(new IconNamespace("b", "{$this->fixtures}/valid/b"));

        self::assertContains(\trim(\file_get_contents("{$this->fixtures}/valid/a/add.svg")), $registry->get("a/add"));
        self::assertContains(\trim(\file_get_contents("{$this->fixtures}/valid/b/add.svg")), $registry->get("b/add"));
        self::assertContains(\trim(\file_get_contents("{$this->fixtures}/valid/a/sub/nested.svg")), $registry->get("a/nested"));
    }


    /**
     * @dataProvider provideDebugAndProd
     *
     * @param bool $debug
     */
    public function testDuplicateNamespace (bool $debug) : void
    {
        $this->expectException(DuplicateNamespaceException::class);
        $registry = new IconRegistry(new ArrayAdapter(), new IconLoader(), "/project/dir/", $debug);

        $registry->registerNamespace(new IconNamespace("test", "test"));
        $registry->registerNamespace(new IconNamespace("test", "test"));
    }


    /**
     * @dataProvider provideDebugAndProd
     *
     * @param bool $debug
     */
    public function testProjectNamespacePrefix () : void
    {
        $loader = $this->getMockBuilder(IconLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $loader
            ->expects(self::once())
            ->method("load")
            ->with("/project/dir/valid/a", "test")
            ->willReturn(["add" => "add"]);

        $registry = new IconRegistry(new ArrayAdapter(), $loader, "/project/dir/", true);

        $registry->registerProjectNamespace("test", "/valid/a", "test");
        $registry->get("test/add");
    }
}
