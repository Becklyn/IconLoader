<?php declare(strict_types=1);

namespace Becklyn\IconLoader\DependencyInjection;

use Becklyn\IconLoader\Registry\IconRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Compiler pass that installs a icon namespace for your bundle.
 */
class RegisterIconNamespacesCompilerPass implements CompilerPassInterface
{
    private array $globalNamespaces;


    public function __construct (array $globalNamespaces)
    {
        $this->globalNamespaces = $globalNamespaces;
    }


    /**
     * @inheritDoc
     */
    public function process (ContainerBuilder $container) : void
    {
        $registry = $container->getDefinition(IconRegistry::class);

        foreach ($this->globalNamespaces as $namespace => $data)
        {
            // support string values
            $config = \is_array($data) ? $data : ["path" => $data];

            $registry->addMethodCall("registerProjectNamespace", [
                $namespace,
                $config["path"],
                $config["class_pattern"] ?? null,
            ]);
        }
    }
}
