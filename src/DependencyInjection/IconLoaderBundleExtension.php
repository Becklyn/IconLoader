<?php declare(strict_types=1);

namespace Becklyn\IconLoader\DependencyInjection;

use Becklyn\IconLoader\Registry\IconRegistry;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class IconLoaderBundleExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load (array $configs, ContainerBuilder $container) : void
    {
        // load services
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . "/../Resources/config")
        );
        $loader->load("services.yaml");

        // map config to services
        $config = $this->processConfiguration(new IconLoaderBundleConfiguration(), $configs);

        $registry = $container->getDefinition(IconRegistry::class);

        foreach ($config["namespaces"] as $namespace => $namespaceConfig)
        {
            $registry->addMethodCall("registerProjectNamespace", [
                $namespace,
                $namespaceConfig["path"],
                $namespaceConfig["class_pattern"],
            ]);
        }
    }


    /**
     * @inheritDoc
     */
    public function getAlias ()
    {
        return "becklyn_icon_loader";
    }
}
