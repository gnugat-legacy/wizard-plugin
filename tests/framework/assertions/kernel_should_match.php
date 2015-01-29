#!/usr/bin/env php
<?php

use Gnugat\Redaktilo\EditorFactory;

require __DIR__.'/../../../vendor/autoload.php';

$pattern = $argv[1];
$editor = EditorFactory::createEditor();

$kernel = $editor->open(__DIR__.'/../../sandbox/app/AppKernel.php');
$hasBundle = $editor->hasBelow($kernel, $pattern);
$failure = sprintf('The pattern "%s" was not found:', $pattern)."\n".implode("\n", $kernel->getLines());

if ($hasBundle) {
    exit(0);
}
die($failure);
