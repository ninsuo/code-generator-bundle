<?php

namespace Bundles\CodeGeneratorBundle\Command;

use Bundles\CodeGeneratorBundle\Repository\SnippetRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Environment;

class SnippetCommand extends Command
{
    protected static $defaultName = 'snippet';

    private SnippetRepository     $snippetRepository;
    private ParameterBagInterface $parameterBag;
    private Environment           $twig;

    public function __construct(SnippetRepository $snippetRepository,
        ParameterBagInterface $parameterBag,
        Environment $twig)
    {
        $this->snippetRepository = $snippetRepository;
        $this->parameterBag      = $parameterBag;
        $this->twig              = $twig;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate a snippet')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Snippet name'
            )
            ->addOption(
                'destination',
                'd',
                InputOption::VALUE_REQUIRED,
                'Destination of generated files',
                $this->parameterBag->get('kernel.project_dir')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $io = new SymfonyStyle($input, $output);

        // Snippet
        $snippets = [];
        foreach ($this->snippetRepository->findAll() as $snippet) {
            $snippets[$snippet->getName()] = $snippet;
        }

        if (null === $name = $input->getArgument('name')) {
            $name = $io->choice(
                'Which snippet would you like to generate',
                array_keys($snippets)
            );
        }

        if (!isset($snippets[$name])) {
            $io->error(sprintf('Unknown snippet: %s', $name));

            return Command::INVALID;
        }

        // Context
        $snippet = $snippets[$name];
        $context = $snippet->createContext();
        do {
            $context = $this->interactToGatherContext($io, $context);

            $io->info('The following context will be used:');
            dump($context);
            $io->writeln('');

        } while (!$io->confirm('Continue?'));

        // Templates
        $templates = $snippet->dumpTemplates($this->twig, false);
        foreach ($templates as $template) {
            $destination = $template['destination'];
            $path        = sprintf('%s/%s', $input->getOption('destination'), $destination);

            if (!is_dir($dir = dirname($destination))) {
                $io->text(sprintf('Create directory <fg=red>%s</>...', $dir));
                mkdir($dir);
            }

            $io->text(sprintf('Generating <fg=red>%s</>...', $path));
            file_put_contents($destination, $template['template']);
        }

        return Command::SUCCESS;
    }

    private function interactToGatherContext(SymfonyStyle $io, array $context, string $prefix = '') : array
    {
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                // Value
                $context[$key] = $io->ask(
                    sprintf('Choose a value for "<fg=blue>%s%s</>"', $prefix, $key),
                    $value
                );
            } elseif ([] === $value || array_keys($value) === range(0, count($value) - 1)) {
                // Sequential array
                $context[$key] = [];

                while ($io->confirm(sprintf('Property "<fg=blue>%s%s</>" is an array, add a value?', $prefix, $key))) {
                    $context[$key][] = $io->ask('Value to add', current($value));
                    next($value);
                }
            } else {
                // Associative array
                $context[$key] = $this->interactToGatherContext($io, $value, sprintf('%s.', $key));
            }
        }

        return $context;
    }
}