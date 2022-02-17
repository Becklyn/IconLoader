<?php declare(strict_types=1);

namespace Becklyn\IconLoader\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class IconLoaderBundleConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder () : TreeBuilder
    {
        $treeBuilder = new TreeBuilder("becklyn_icon_loader");
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->arrayNode("namespaces")
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function (string $value) { return ["path" => $value]; })
                        ->end()
                        ->children()
                            ->scalarNode("path")
                                ->isRequired()
                            ->end()
                            ->scalarNode("class_pattern")
                                ->defaultNull()
                                ->info("The class name pattern. The icon name will be passed and can be embedded using sprintf syntax '%s'.")
                            ->end()
                        ->end()
                    ->end()
                    ->info("The mapping of namespace to directory. Relative to the project dir.")
                ->end()
            ->end();

        return $treeBuilder;
    }

}
