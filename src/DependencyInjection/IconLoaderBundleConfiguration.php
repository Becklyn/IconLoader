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
    public function getConfigTreeBuilder ()
    {
        $treeBuilder = new TreeBuilder("becklyn_icon_loader");
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode("search_glob")
                    ->defaultValue("build/mayd/*/icon")
                    ->info("The glob to the directories, where the icons are stored. Will search for all *.svg icons there. Relative to the project dir.")
                ->end()
            ->end();

        return $treeBuilder;
    }

}
