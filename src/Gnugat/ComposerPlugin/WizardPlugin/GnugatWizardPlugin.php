<?php

/*
 * This file is part of the GnugatWizardPlugin project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\ComposerPlugin\WizardPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

use Gnugat\ComposerPlugin\WizardPlugin\DependencyInjection\Factory;

/**
 * On package installation, checks wether or not it's a bundle and if so
 * register it in the application's kernel.
 *
 * @author Loïc Chardonnet <loic.chardonnet@gmail.com>
 */
class GnugatWizardPlugin implements PluginInterface, EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_PACKAGE_INSTALL => array(
                array('registerPackage', 0)
            ),
        );
    }

    /**
     * On Composer's "post-package-install" event, register the package.
     *
     * @param Event $event
     */
    public function registerPackage(Event $event)
    {
        $installedPackage = $event->getOperation()->getPackage();

        if (!$this->supports($installedPackage)) {
            return;
        }

        $this->enablePackage($installedPackage);
    }

    /**
     * Checks if the context is supported.
     *
     * @param PackageInterface $package
     *
     * @return bool
     */
    public function supports(PackageInterface $package)
    {
        $isSymfony2Bundle = ('symfony-bundle' === $package->getType());

        return $isSymfony2Bundle;
    }

    /**
     * @param PackageInterface $package
     *
     * @throws \RuntimeException If an error occured during the running
     */
    public function enablePackage(PackageInterface $package)
    {
        $packageRepository = Factory::makePackageRepository($package);
        $bundleFactory = Factory::makeBundleFactory();
        $kernelManipulator = Factory::makeKernelManipulator();

        $composerPackage = $packageRepository->findOneByName($package->getName());
        $bundle = $bundleFactory->make($composerPackage->namespace);

        $kernelManipulator->addBundle($bundle->fullyQualifiedClassname);
    }
}
