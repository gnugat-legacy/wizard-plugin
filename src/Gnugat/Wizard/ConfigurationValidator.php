<?php

/*
 * This file is part of the GnugatWizardBundle project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\Wizard;

use Symfony\Component\Config\Definition\Processor;
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

/**
 * Validate the app configuration
 *
 * @author Jérémy Romey <jeremy@free-agent.fr>
 */
class ConfigurationValidator
{
    protected $kernel;
    protected $class;

    public function validate($projectPath, $configFileName, $environment)
    {
        require sprintf('%s/vendor/autoload.php', $projectPath);

        $configFile = sprintf('%s/app/config/%s', $projectPath, $configFileName);

        $this->kernel = $this->createKernel($projectPath, array('environment' => $environment));

        $config1 = Yaml::parse($configFile);
        // $config2 = Yaml::parse(__DIR__.'/src/Matthias/config/config_extra.yml');

        // $configs = array($config1, $config2);

        $configs = $config1;

        foreach ($configs['imports'] as $import) {
            $importConfig = Yaml::parse(sprintf('%s/app/config/%s', $projectPath, $import['resource']));
            $configs = array_merge($configs, $importConfig);
        }

        unset($configs['imports']);

        $missings = array();

        $processor = new Processor();
        foreach ($this->kernel->registerBundles() as $bundle) {
            // $extension = new \Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension();
            // $configuration = new \Symfony\Bundle\FrameworkBundle\DependencyInjection\Configuration();

            $extension = $bundle->getContainerExtension();
            $reflected = new \ReflectionClass($extension);
            $namespace = $reflected->getNamespaceName();

            $class = $namespace.'\\Configuration';
            if (class_exists($class) && !method_exists($class, '__construct') && array_key_exists($extension->getAlias(), $configs)) {
                $configuration = new $class;

                try {
                    $processedConfiguration = $processor->processConfiguration($configuration, array($extension->getAlias() => $configs[$extension->getAlias()]));
                } catch (\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException $e) {
                    // preg_match('/The child node "([a-z]*)" at path "([a-z]*\.[a-z]*)" must be configured\./', $e->getMessage(), $matches);
                    preg_match('/The child node "([a-z]*)"/', $e->getMessage(), $matches);

                    $missings[$extension->getAlias()][$e->getPath()][] = $matches[1];

                    return $e->getPath() . '.' . $matches[1];
                }
            }
        }

        return true;
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

    // public function rebootKernel()
    // {
    //     if ($this->kernel) {
    //         $this->kernel->shutdown();
    //     }
    //     $this->kernel = $this->createKernel();
    //     $this->kernel->boot();
    // }

    public function createKernel($projectPath, $options)
    {
        if (null === $this->class) {
            $this->class = $this->getKernelClass($projectPath);
        }

        return new $this->class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true
        );
    }

    protected function getKernelClass($projectPath)
    {
        $dir = $this->getKernelDirectory($projectPath);

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

    protected function getKernelDirectory($projectPath)
    {
        $dir = sprintf('%s/app', $projectPath);
        if (!is_dir($dir)) {
            $dir = dirname($dir);
        }

        return $dir;
    }
}
