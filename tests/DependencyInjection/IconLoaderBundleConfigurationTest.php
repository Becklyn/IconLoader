<?php declare(strict_types=1);

namespace Tests\Becklyn\IconLoader\DependencyInjection;

use Becklyn\IconLoader\DependencyInjection\IconLoaderBundleConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class IconLoaderBundleConfigurationTest extends TestCase
{
    /**
     * @return array
     */
    public function provideProcessValues () : array
    {
        return [
            "empty" => [
                [],
                ["namespaces" => []],
            ],
            "variations" => [
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_pattern" => "class_pattern-test",
                    ],
                    "test2" => [
                        "path" => "path-test2",
                    ],
                    "test3" => "path-test2",
                ]],
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_pattern" => "class_pattern-test",
                    ],
                    "test2" => [
                        "path" => "path-test2",
                        "class_pattern" => null,
                    ],
                    "test3" => [
                        "path" => "path-test2",
                        "class_pattern" => null,
                    ],
                ]],
            ],
            "disable class pattern" => [
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_pattern" => "",
                    ],
                ]],
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_pattern" => "",
                    ],
                ]],
            ],
        ];
    }


    /**
     * @dataProvider provideProcessValues
     *
     * @param array $config
     * @param array $expected
     */
    public function testProcessValues (array $config, array $expected) : void
    {
        $processor = new Processor();
        $processed = $processor->processConfiguration(new IconLoaderBundleConfiguration(), [
            "becklyn_icon_loader" => $config,
        ]);

        static::assertSame($expected, $processed);
    }
}
