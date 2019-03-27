<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Loader;


use Becklyn\IconLoader\Exception\IconConflictException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class IconLoader
{
    /**
     * @var string
     */
    private $directoriesGlob;


    /**
     *
     * @param string $directoriesGlob
     */
    public function __construct (string $directoriesGlob)
    {
        $this->directoriesGlob = $directoriesGlob;
    }


    /**
     * Loads a map of all icon keys to their content.
     *
     * Will overwrite existing keys, so if an icon is found multiple times,
     *
     * @return array
     */
    public function load () : array
    {
        try
        {
            $finder = Finder::create()
                ->in($this->directoriesGlob)
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

                // icon with same key found
                if (isset($mapping[$key]))
                {
                    // different content -> throw exception
                    if ($content !== $mapping[$key])
                    {
                        throw new IconConflictException($key);
                    }

                    // same content -> ignore
                    continue;
                }

                $mapping[$key] = \trim(\file_get_contents($file->getPathname()));
            }

            return $mapping;
        }
        catch (\InvalidArgumentException $e)
        {
            return [];
        }
    }


    /**
     * Fetches all directories to search in
     *
     * @return array
     */
    private function getDirectories () : array
    {

    }
}
