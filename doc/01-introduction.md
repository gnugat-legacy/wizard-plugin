# Introduction

This project is a [Composer](http://getcomposer.org/) plugin.

Once installed, it will:

1. listen to Composer's `require` command
2. check if the installed package is a [Symfony2](http://symfony.com/) bundle
3. if so:
    * convert the package name into a fully qualified classname (FCQN)
    * register the FCQN in the application's kernel

It heavily relies on [GnugatWizardBundle](https://github.com/gnugat/GnugatWizardBundle).

## What's the point?

Without anything, a bundle installation goes though those steps:

1. `composer require '<package>'`
2. manual edition of `app/AppKernel.php` file

With the GnugatWizardBundle, bundle installation goes through those steps:

1. `composer require '<package>'`
2. `app/console wizard:register:package '<package>'`

With this plugin, bundle installation goes through this single step:

1. `composer require '<package>'`
