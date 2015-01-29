#!/usr/bin/env bash

__DIR__="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

rm -rf $__DIR__/../sandbox
cp -r $__DIR__/../fixtures $__DIR__/../sandbox
cd $__DIR__/../sandbox
composer install > /dev/null
