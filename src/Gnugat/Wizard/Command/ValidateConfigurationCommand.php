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

    /**
     * {@inheritdoc}
     */
    public function eeexecute(InputInterface $input, OutputInterface $output)
    {
        $this->processor = new Processor();

        $this->kernel = $this->createKernel(array('environment' => 'prod'));
        $this->kernel->initializeBundles();
        // $this->kernel->initializeContainer();

        $this->containerBuilder = new ContainerBuilder();
        $this->containerBuilder = $this->kernel->buildContainer();
        $this->containerBuilder->compile();

        foreach ($this->kernel->getBundles() as $bundle) {
            $bundle->setContainer($this->containerBuilder);
            // $bundle->boot();
        }

        // foreach ($this->kernel->getBundles() as $bundle) {
        //     $extension = $bundle->getContainerExtension();
        // }

        // $this->kernel->initializeBundles();
        // $this->processor = new Processor();

        $extension = new \Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension();

        // $configs = array(
        //     'http_method_override' => 'toto',
        // );

        $configuration = $extension->getConfiguration(array(), $this->containerBuilder);

        // $config = $this->getDefaultConfiguration($extension, array());

        // print_r($config);

        // $defaultConfig = $this->getDefaultConfiguration($extension);
        // $config = $this->getConfiguration($extension);

        // print_r($config);

        // try {
        //     $this->rebootKernel();
        // } catch (ParameterNotFoundException $e) {
        //     $output->writeln(sprintf('Missing params <info>%s</info> !', $e->getKey()));
        // }

        $output->writeln('Done');
    }

    public function getDefaultConfiguration(Extension $extension)
    {
        $container = new ContainerBuilder();
        $configuration = $extension->getConfiguration(array(), $container);

        return $this->processor->processConfiguration($configuration, array());
    }

    public function getConfiguration(Extension $extension, $configs = array())
    {
        // $container = $this->getContainerBuilder();
        // $container = new ContainerBuilder();

        $configs = array(

        );
        // $this->kernel->prepareContainer($container);
        $configuration = $extension->getConfiguration(array(), $this->containerBuilder);

        return $this->processor->processConfiguration($configuration, $configs);
    }

    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     *
     * @return ContainerBuilder
     */
    protected function getContainerBuilder()
    {
        $container = new ContainerBuilder(/*new ParameterBag($this->kernel->getKernelParameters())*/);

        if (class_exists('ProxyManager\Configuration')) {
            $container->setProxyInstantiator(new RuntimeInstantiator());
        }

        if (null !== $cont = $this->kernel->registerContainerConfiguration($this->getContainerLoader($container))) {
            $container->merge($cont);
        }

        return $container;
    }


    protected function getContainerLoader(ContainerInterface $container)
    {
        $locator = new FileLocator($this->kernel);
        $resolver = new LoaderResolver(array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator),
            new IniFileLoader($container, $locator),
            new PhpFileLoader($container, $locator),
            new ClosureLoader($container),
        ));

        return new DelegatingLoader($resolver);
    }

    public function rebootKernel()
    {
        if ($this->kernel) {
            $this->kernel->shutdown();
        }
        $this->kernel = $this->createKernel();
        $this->kernel->boot();
    }

    public function createKernel($options)
    {
        if (null === $this->class) {
            $this->class = $this->getKernelClass();
        }

        return new $this->class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    protected function getKernelClass()
    {
        $dir = $this->getKernelDirectory();

        $finder = new Finder();
        $finder->name('*Kernel.php')->depth(0)->in($dir);
        $results = iterator_to_array($finder);
        if (!count($results)) {
            throw new \RuntimeException('Impossible to find a Kernel file.');
        }

        $file = current($results);
        $class = $file->getBasename('.php');

        require_once $file;

        return $class;
    }

    protected function getKernelDirectory()
    {
        $dir = getcwd().'/../project-test/app';
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }

    /*
     $process = new PhpProcess(<<<EOF
    <?php throw new \Exception('ici'); ?>
EOF
);
        $output->writeln('--------- PhpProcess ---------');

        $process->run();
        // if (!$process->isSuccessful()) {
        //     $output->writeln('--------- FOUND ---------');
        //     print $process->getErrorOutput();
        // }
        // print $process->getOutput();
        $output->writeln('--------- END ---------');

        $process = new Process('php ../project-test/app/console');
        $process->setTimeout(3600);
        $output->writeln('--------- START ---------');
        $process->run();
        if (!$process->isSuccessful()) {
            $output->writeln('--------- FOUND ---------');
            print $process->getErrorOutput();
        }

        print $process->getOutput();

        $output->writeln('--------- END ---------');
     */
}
