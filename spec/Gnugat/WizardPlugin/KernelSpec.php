<?php

namespace spec\Gnugat\WizardPlugin;

use Gnugat\Redaktilo\Editor;
use Gnugat\Redaktilo\File;
use PhpSpec\ObjectBehavior;

class KernelSpec extends ObjectBehavior
{
    const FILENAME = '/tmp/AppKernel.php';
    const FULLY_QUALIFIED_CLASS_NAME = 'Acme\\DemoBundle\\AcmeDemoBundle';
    const BUNDLE_LINE = '            new Acme\\DemoBundle\\AcmeDemoBundle(),';

    function let(Editor $editor)
    {
        $this->beConstructedWith($editor, self::FILENAME);
    }

    function it_registers_new_bundles(Editor $editor, File $kernel)
    {
        $editor->open(self::FILENAME)->willReturn($kernel);
        $editor->hasBelow($kernel, self::BUNDLE_LINE)->willReturn(true);
        $editor->jumpBelow($kernel, '        );')->shouldBeCalled();
        $editor->insertAbove($kernel, self::BUNDLE_LINE)->shouldBeCalled();
        $editor->save($kernel)->shouldBeCalled();

        $this->registerBundle(self::FULLY_QUALIFIED_CLASS_NAME);
    }

    function it_does_not_register_already_registered_bundles(Editor $editor, File $kernel)
    {
        $editor->open(self::FILENAME)->willReturn($kernel);
        $editor->hasBelow($kernel, self::BUNDLE_LINE)->willReturn(false);
        $editor->jumpBelow($kernel, '        );')->shouldNotBeCalled();
        $editor->insertAbove($kernel, self::BUNDLE_LINE)->shouldNotBeCalled();
        $editor->save($kernel)->shouldNotBeCalled();

        $this->registerBundle(self::FULLY_QUALIFIED_CLASS_NAME);
    }
}
