<?php

declare(strict_types=1);

namespace Mihaeu\PhpDependencies\Cli;

use Mihaeu\PhpDependencies\Dependencies\DependencyMap;
use Mihaeu\PhpDependencies\OS\PlantUmlWrapper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UmlCommand extends BaseCommand
{
    /** @var PlantUmlWrapper */
    private $plantUmlWrapper;

    /**
     * @param DependencyMap $dependencies
     * @param \Closure $postProcessors
     * @param PlantUmlWrapper $plantUmlWrapper
     */
    public function __construct(
        DependencyMap $dependencies,
        \Closure $postProcessors,
        PlantUmlWrapper $plantUmlWrapper
    ) {
        parent::__construct('uml', $dependencies, $postProcessors);

        $this->plantUmlWrapper = $plantUmlWrapper;

        $this->defaultFormat = 'png';
        $this->allowedFormats = [$this->defaultFormat];
    }

    protected function configure()
    {
        parent::configure();

        $this
            ->setDescription('Generate a UML Class diagram of your dependencies')
            ->addOption(
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                'Destination for the generated class diagram (in .png format).'
            )
            ->addOption(
                'keep-uml',
                null,
                InputOption::VALUE_NONE,
                'Keep the intermediate PlantUML file instead of deleting it.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOptions();
        $this->ensureSourcesAreReadable($input->getArgument('source'));
        $this->ensureOutputExists($options['output']);
        $this->ensureDestinationIsWritable($options['output']);
        $this->ensureOutputFormatIsValid($options['output']);

        $destination = new \SplFileInfo($options['output']);
        $this->plantUmlWrapper->generate(
            $this->dependencies->reduceEachDependency($this->postProcessors),
            $destination,
            $options['keep-uml']
        );
    }

    /**
     * @param $outputOption
     *
     * @throws \InvalidArgumentException
     */
    private function ensureOutputExists($outputOption)
    {
        if ($outputOption === null) {
            throw new \InvalidArgumentException('Output not defined (use "help" for more information).');
        }
    }
}
