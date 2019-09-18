<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Loader;

use Becklyn\IconLoader\Exception\IconConflictException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class IconLoader
{
    /**
     * Loads a map of all icon keys to their content.
     *
     * Will overwrite existing keys, so if an icon is found multiple times,
     *
     * @param string $directory
     * @param string $classPattern
     *
     * @return array
     */
    public function load (string $directory, string $classPattern = "") : array
    {
        try
        {
            $finder = Finder::create()
                ->in($directory)
                ->files()
                ->name("*.svg")
                ->ignoreDotFiles(true);

            if (!$finder->hasResults())
            {
                return [];
            }

            $mapping = [];

            /** @var SplFileInfo $file */
            foreach ($finder as $file)
            {
                $key = $file->getBasename(".{$file->getExtension()}");
                $content = \trim(\file_get_contents($file->getPathname()));

                $attributes = ("" !== $classPattern)
                    ? ' class="' . \sprintf($classPattern, $key) . '"'
                    : "";

                $finalIcon = "<span{$attributes}>{$content}</span>";

                // icon with same key found
                if (isset($mapping[$key]))
                {
                    // different content -> throw exception
                    if ($finalIcon !== $mapping[$key])
                    {
                        throw new IconConflictException($key);
                    }

                    // same content -> ignore
                    continue;
                }

                $mapping[$key] = $finalIcon;
            }

            return $mapping;
        }
        catch (\InvalidArgumentException $e)
        {
            return [];
        }
    }
}
