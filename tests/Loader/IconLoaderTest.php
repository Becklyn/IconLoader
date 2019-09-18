<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Loader;

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
        $loader = new IconLoader();
        $map = $loader->load(__DIR__ . "/../_fixtures/valid");

        static::assertCount(3, $map);
        static::assertSame("<span>add</span>", $map["add"]);
        static::assertSame("<span>remove</span>", $map["remove"]);
        static::assertSame("<span>nested</span>", $map["nested"]);
    }


    /**
     *
     */
    public function testConflict () : void
    {
        $this->expectException(IconConflictException::class);
        $this->expectExceptionMessage("Conflict for icon 'conflict'.");

        $loader = new IconLoader();
        $loader->load(__DIR__ . "/../_fixtures/different_files");
    }


    /**
     *
     */
    public function testMissingDir () : void
    {
        $loader = new IconLoader();
        static::assertSame([], $loader->load(__DIR__ . "/../_fixtures/missing"));
    }


    /**
     * Tests the proper wrapping
     */
    public function testWrapping () : void
    {
        $loader = new IconLoader();
        $icons = $loader->load(__DIR__ . "/../_fixtures/valid/a", "icon icon-%s");
        self::assertSame('<span class="icon icon-add">add</span>', $icons["add"]);
    }


    public function testEmptyWrapping () : void
    {
        $loader = new IconLoader();
        $icons = $loader->load(__DIR__ . "/../_fixtures/valid/a", "");
        self::assertSame('<span>add</span>', $icons["add"]);
    }
}
