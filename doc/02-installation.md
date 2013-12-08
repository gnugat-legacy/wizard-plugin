# Installation

If you want to install this project on its own to test it or to contribute to
it, follow these commands:

    git clone https://github.com/gnugat/wizard-plugin
    cd wizard-plugin
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install

If you'd rather want to install the plugin in a [Symfony2](http://symfony.com/)
application, use the `bin/installer.sh` script instead. It allows you to do
automagically all the following steps.

## 1. Dependency manager

First of all, it checks if [Composer](http://getcomposer.org/) is locally
installed. If not, it downloads it using:

    curl -sS https://getcomposer.org/installer | php

## 2. Downloading

Then it uses Composer to download the plugin:

    php composer.phar require "gnugat/wizard-plugin:~1"

This will also download
[GnugatWizardBundle](https://github.com/gnugat/GnugatWizardBundle).

## 3. Registering the bundle

*Heads up*: This is the kind of step this plugin allows you to skip!

Finally it simply registers the bundle by adding its fully qualified classname
in the application's kernel, for example like this:

    <?php
    // File: app/AppKernel.php

    use Symfony\Component\HttpKernel\Kernel;

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // Other bundles...
            );

            if (in_array($this->getEnvironment(), array('dev', 'test'))) {
                // Other bundles...
                $bundles[] = new Gnugat\Bundle\WizardBundle\GnugatWizardBundle();
            }

            return $bundles;
        }
    }

## 4. The end

Each time you'll run `composer require <package>`, this plugin will check if
the installed package is a bundle, and if so it will automagically register it
in your application's kernel.
