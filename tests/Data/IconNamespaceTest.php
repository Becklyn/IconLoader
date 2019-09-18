<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Data;

use Becklyn\IconLoader\Data\IconNamespace;
use PHPUnit\Framework\TestCase;

class IconNamespaceTest extends TestCase
{
    public function testFull ()
    {
        $namespace = new IconNamespace("key", "dir", "class");

        self::assertSame("key", $namespace->getKey());
        self::assertSame("dir", $namespace->getDirectory());
        self::assertSame("class", $namespace->getClassPattern());
    }


    public function testDefaultClass ()
    {
        $namespace = new IconNamespace("key", "dir");

        self::assertSame("key", $namespace->getKey());
        self::assertSame("dir", $namespace->getDirectory());
        self::assertSame("icon icon-%s", $namespace->getClassPattern());
    }
}
