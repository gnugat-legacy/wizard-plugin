# Introduction

This project is a [Composer](http://getcomposer.org/) plugin.

Once installed, it will:

1. listen to Composer's `require` command
2. check if the installed package is a [Symfony2](http://symfony.com/) bundle
3. if so, register it in the application's kernel

It uses [GnugatWizardBundle](https://github.com/gnugat/GnugatWizardBundle) for
the bundle registration.

## What's the point?

Without anything, a bundle installation goes though those steps:

1. `composer require '<package>'`
2. manual edition of `app/AppKernel.php` file

With the GnugatWizardBundle, bundle installation goes through those steps:

1. `composer require '<package>'`
2. `app/console wizard:register:package '<package>'`

With this plugin, bundle installation goes through this single step:

1. `composer require '<package>'`
