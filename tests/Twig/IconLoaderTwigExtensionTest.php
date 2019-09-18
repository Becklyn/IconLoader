<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\Twig;

use Becklyn\IconLoader\Registry\IconRegistry;
use Becklyn\IconLoader\Twig\IconLoaderTwigExtension;
use PHPUnit\Framework\TestCase;
use Twig\TwigFunction;

class IconLoaderTwigExtensionTest extends TestCase
{
    /**
     *
     */
    public function testDefinedFunctions () : void
    {
        $registry = $this->getMockBuilder(IconRegistry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $extension = new IconLoaderTwigExtension($registry);
        $functionNames = \array_map(
            function (TwigFunction $function)
            {
                return $function->getName();
            },
            $extension->getFunctions()
        );

        self::assertContains("icon", $functionNames);
    }
}
