<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Registry;

use Becklyn\IconLoader\Exception\IconConflictException;
use Becklyn\IconLoader\Loader\IconLoader;
use PHPUnit\Framework\TestCase;

class IconLoaderTest extends TestCase
{
    public function testValid ()
    {
        $loader = new IconLoader(__DIR__ . "/../_fixtures/valid/*/icon");
        $map = $loader->load();

        self::assertCount(3, $map);
        self::assertSame("add", $map["add"]);
        self::assertSame("remove", $map["remove"]);
        self::assertSame("nested", $map["nested"]);
    }


    public function testConflict ()
    {
        $this->expectException(IconConflictException::class);
        $this->expectExceptionMessage("Conflict for icon 'conflict'.");

        $loader = new IconLoader(__DIR__ . "/../_fixtures/different_files/*/icon");
        $map = $loader->load();
    }
}
