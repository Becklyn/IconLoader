<?php declare(strict_types=1);

namespace Becklyn\IconLoader;

use Becklyn\IconLoader\DependencyInjection\IconLoaderBundleExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BecklynIconLoaderBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension ()
    {
        return new IconLoaderBundleExtension();
    }
}
