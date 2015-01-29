<?php

/*
 * This file is part of the GnugatWizardPlugin project.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\WizardPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Gnugat\WizardPlugin\Container;

/**
 * Registers new bundles in Symfony's AppKernel.
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
                array('onPackageInstalled', 0)
            ),
        );
    }

    /**
     * @param Event $event
     */
    public function onPackageInstalled(Event $event)
    {
        $installedPackage = $event->getOperation()->getPackage();
        if ('symfony-bundle' !== $installedPackage->getType()) {
            return;
        }
        $container = new Container('app/AppKernel.php');
        $bundleFqcn = $container->getPackageNameConvertor()->toBundleFqcn($installedPackage->getName());
        $container->getKernel()->registerBundle($bundleFqcn);
    }
}
