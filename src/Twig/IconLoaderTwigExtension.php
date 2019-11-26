<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Twig;

use Becklyn\IconLoader\Registry\IconRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconLoaderTwigExtension extends AbstractExtension
{
    /**
     * @var IconRegistry
     */
    private $iconRegistry;


    /**
     */
    public function __construct (IconRegistry $iconRegistry)
    {
        $this->iconRegistry = $iconRegistry;
    }


    /**
     * @inheritDoc
     */
    public function getFunctions ()
    {
        return [
            new TwigFunction("icon", [$this->iconRegistry, "get"], ["is_safe" => ["html"]]),
        ];
    }
}
