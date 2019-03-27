<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Exception\IconConflictException;
use Becklyn\IconLoader\Loader\IconLoader;
use PHPUnit\Framework\TestCase;

class IconLoaderTest extends TestCase
{
    /**
     *
     */
    public function testValid () : void
    {
        $loader = new IconLoader(__DIR__ . "/../_fixtures/valid/*/icon");
        $map = $loader->load();

        static::assertCount(3, $map);
        static::assertSame("add", $map["add"]);
        static::assertSame("remove", $map["remove"]);
        static::assertSame("nested", $map["nested"]);
    }


    /**
     *
     */
    public function testConflict () : void
    {
        $this->expectException(IconConflictException::class);
        $this->expectExceptionMessage("Conflict for icon 'conflict'.");

        $loader = new IconLoader(__DIR__ . "/../_fixtures/different_files/*/icon");
        $loader->load();
    }


    /**
     *
     */
    public function testMissingDir () : void
    {
        $loader = new IconLoader(__DIR__ . "/../_fixtures/different_files/*/missing");
        static::assertSame([], $loader->load());
    }
}
