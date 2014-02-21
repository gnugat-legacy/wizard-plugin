<?php

/*
 * This file is part of the GnugatWizardBundle project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\Wizard\Command;

use Symfony\Component\Console\Command\Command;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Yaml\Yaml;

use Gnugat\Wizard\ConfigurationValidator;

/**
 * Validate the app configuration
 *
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class ValidateConfigurationCommand extends Command
{
    protected $kernel;
    protected $class;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
            ->setName('wizard:validate:configuration')
            ->addArgument(
                'project-path',
                InputArgument::REQUIRED,
                'Project path to validate'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $configurationValidator = new ConfigurationValidator();

        $projectPath = getcwd() . '/' . $input->getArgument('project-path');

        $configFileName = 'config_prod.yml';
        $environment = 'prod';

        $missings = $configurationValidator->validate($projectPath, $configFileName, $environment);

        print_r($missings);
    }
}
