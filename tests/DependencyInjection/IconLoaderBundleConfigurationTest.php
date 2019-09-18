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
            [
                [],
                ["namespaces" => []],
            ],
            [
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_name" => "class_name-test",
                    ],
                    "test2" => [
                        "path" => "path-test2",
                    ],
                    "test3" => "path-test2",
                ]],
                ["namespaces" => [
                    "test" => [
                        "path" => "path-test",
                        "class_name" => "class_name-test",
                    ],
                    "test2" => [
                        "path" => "path-test2",
                        "class_name" => null,
                    ],
                    "test3" => [
                        "path" => "path-test2",
                        "class_name" => null,
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
            "becklyn_icon_loader" => $config
        ]);

        self::assertEquals($expected, $processed);
    }
}
