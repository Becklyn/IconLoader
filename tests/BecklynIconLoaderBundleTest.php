<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader;

use Becklyn\IconLoader\BecklynIconLoaderBundle;
use Becklyn\IconLoader\DependencyInjection\IconLoaderBundleExtension;
use PHPUnit\Framework\TestCase;

class BecklynIconLoaderBundleTest extends TestCase
{
    /**
     *
     */
    public function testExtension () : void
    {
        $bundle = new BecklynIconLoaderBundle();
        self::assertInstanceOf(IconLoaderBundleExtension::class, $bundle->getContainerExtension());
    }
}
