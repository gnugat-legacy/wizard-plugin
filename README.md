# [Composer](http://getcomposer.org/) plugin for Gnugat's Wizard

In the Wizard family,
[the big brother (the bundle)](https://github.com/gnugat/GnugatWizardBundle)
provides a command to register a [Symfony2](http://symfony.com/) bundle into
the application's kernel.

This project, the little sister, is a Composer plugin which launches this
command after each:

    php composer.phar require '<package>'

If you want to learn more about the big brother, read
[its own introduction](https://github.com/gnugat/GnugatWizardBundle/blob/master/Resources/doc/01-introduction.md#introduction).
If you'd rather learn more about the little sister, read
[this introduction](doc/01-introduction.md).

## Installation

To download and install this plugin into your Symfony2 application, run the
following command:

    curl -sS  https://raw.github.com/gnugat/wizard-plugin/master/bin/installer.sh | sh

Details about installation steps can be found in the
[documentation](doc/02-installation.md).

## Further documentation

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/gnugat/wizard-plugin/releases)
* the file listing the [changes between versions](CHANGELOG.md)

You can find more documentation at the following links:

* [copyright and MIT license](LICENSE)
* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)
* [documentation directory](doc)

This plugin began as a hackday at [SensioLabs](http://sensiolabs.com/), with
the help of:

* [Loïc Chardonnet](https://github.com/gnugat) (lead developer)
* [Frank Stelzer](https://github.com/frastel)
* and other
  [awesome developers](https://github.com/gnugat/wizard-plugin/graphs/contributors)
