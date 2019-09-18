<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\DependencyInjection;

use Becklyn\IconLoader\DependencyInjection\IconLoaderBundleExtension;
use Becklyn\IconLoader\Registry\IconRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class IconLoaderBundleExtensionTest extends TestCase
{
    /**
     *
     */
    public function testAlias () : void
    {
        $extension = New IconLoaderBundleExtension();
        self::assertSame("icon_loader", $extension->getAlias());
    }


    /**
     * Tests that the config and the extension are correctly wired
     */
    public function testIntegration () : void
    {
        $container = new ContainerBuilder(new ParameterBag());

        $config = [
            "icon_loader" => [
                "namespaces" => [
                    "app" => "app",
                    "build" => "build",
                ],
            ],
        ];

        self::assertFalse($container->hasDefinition(IconRegistry::class));

        $extension = New IconLoaderBundleExtension();
        $extension->load($config, $container);

        $registry = $container->getDefinition(IconRegistry::class);
        $methodCalls = $registry->getMethodCalls();

        self::assertCount(2, $methodCalls);
        self::assertSame(["registerProjectNamespace", ["app", "app", null]], $methodCalls[0]);
        self::assertSame(["registerProjectNamespace", ["build", "build", null]], $methodCalls[1]);
    }
}
