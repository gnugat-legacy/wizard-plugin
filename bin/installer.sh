#!/bin/sh

if ! -f "composer.phar" 2>/dev/null; then
    echo '[curl] Getting Composer, the PHP dependency manager'
    curl -sS https://getcomposer.org/installer | php
fi

echo '[composer] Downloading the bundle'
php composer.phar require "gnugat/wizard-plugin:~1"

echo '[sed] Enabling the bundle'
sed -i 's/        }/            $bundles[] = new Gnugat\\Bundle\\WizardBundle\\GnugatWizardBundle();\n        }/' app/AppKernel.php
