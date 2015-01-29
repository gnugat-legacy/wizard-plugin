#!/usr/bin/env bash

__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

cd $__DIR__/sandbox
composer require 'symfony/framework-bundle' > /dev/null
cd $__DIR__

php $__DIR__/framework/assertions/kernel_should_match.php '/new Acme\DemoBundle\AcmeDemoBundle()/'
