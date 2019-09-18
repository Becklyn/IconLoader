<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Data;

use Becklyn\IconLoader\Data\IconNamespace;
use Becklyn\IconLoader\Exception\InvalidNamespaceKeyException;
use PHPUnit\Framework\TestCase;

class IconNamespaceTest extends TestCase
{
    /**
     *
     */
    public function testFull () : void
    {
        $namespace = new IconNamespace("key", "dir", "class");

        static::assertSame("key", $namespace->getKey());
        static::assertSame("dir", $namespace->getDirectory());
        static::assertSame("class", $namespace->getClassPattern());
    }


    /**
     *
     */
    public function testDefaultClass () : void
    {
        $namespace = new IconNamespace("key", "dir");

        static::assertSame("key", $namespace->getKey());
        static::assertSame("dir", $namespace->getDirectory());
        static::assertSame("icon icon-%s", $namespace->getClassPattern());
    }


    /**
     *
     */
    public function testInvalidNamespaceKey () : void
    {
        $this->expectException(InvalidNamespaceKeyException::class);
        new IconNamespace("invalid/invalid", "test");
    }
}
