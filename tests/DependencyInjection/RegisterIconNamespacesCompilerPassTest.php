<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\DependencyInjection;

use Becklyn\IconLoader\DependencyInjection\RegisterIconNamespacesCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterIconNamespacesCompilerPassTest extends TestCase
{
    public function testPassingArguments ()
    {
        $containerBuilder = $this->getMockBuilder(ContainerBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $definition = $this->getMockBuilder(Definition::class)
            ->disableOriginalConstructor()
            ->getMock();

        $pass = new RegisterIconNamespacesCompilerPass([
            "test" => [
                "path" => "test-path",
                "class_name" => "test-class",
            ],
            "test2" => [
                "path" => "test2-path",
            ],
            "test3" => "test3-path",
        ]);

        $containerBuilder
            ->expects(self::once())
            ->method("getDefinition")
            ->willReturn($definition);

        $definition
            ->expects(self::exactly(3))
            ->method("addMethodCall")
            ->withConsecutive(
                ["registerProjectNamespace", ["test", "test-path", "test-class"]],
                ["registerProjectNamespace", ["test2", "test2-path", null]],
                ["registerProjectNamespace", ["test3", "test3-path", null]]
            );

        $pass->process($containerBuilder);
    }
}
