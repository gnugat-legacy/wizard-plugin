<?php

namespace spec\Gnugat\WizardPlugin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PackageNameConvertorSpec extends ObjectBehavior
{
    const PACKAGE_NAME = 'acme/demo-bundle';
    const BUNDLE_FQCN = 'Acme\\DemoBundle\\AcmeDemoBundle';

    function it_converts_package_name_to_bundle_fully_qualified_class_name()
    {
        $this->toFqcn(self::PACKAGE_NAME)->shouldBe(self::BUNDLE_FQCN);
    }
}
