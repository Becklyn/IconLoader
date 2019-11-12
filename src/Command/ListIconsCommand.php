<?php declare(strict_types=1);

namespace Becklyn\IconLoader\Command;

use Becklyn\IconLoader\Registry\IconRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListIconsCommand extends Command
{
    protected static $defaultName = "becklyn:icons:list";


    /**
     * @var IconRegistry
     */
    private $iconRegistry;


    /**
     * @inheritDoc
     */
    public function __construct (IconRegistry $iconRegistry)
    {
        parent::__construct();
        $this->iconRegistry = $iconRegistry;
    }


    /**
     * @inheritDoc
     */
    protected function execute (InputInterface $input, OutputInterface $output) : ?int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("List Icons");

        $namespaces = $this->iconRegistry->getAllNamespaceKeys();

        if (empty($namespaces))
        {
            $io->warning("No icon namespaces registered.");
            return 0;
        }

        return $this->listIconsForNamespace(
            $io,
            1 < \count($namespaces)
                ? $io->choice("Select namespace", $namespaces)
                : $namespaces[0]
        );
    }


    /**
     * Lists all icons for the given namespace key.
     */
    private function listIconsForNamespace (SymfonyStyle $io, string $namespaceKey) : ?int
    {
        $io->comment("Listing icons in namespace <fg=blue>{$namespaceKey}</>");

        $icons = $this->iconRegistry->getIconsInNamespace($namespaceKey);

        if (empty($icons))
        {
            $io->warning("No icons registered in this namespace");
        }
        else
        {
            $io->listing($icons);
        }

        return 0;
    }
}
