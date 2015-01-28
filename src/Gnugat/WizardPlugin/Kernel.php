<?php

namespace Gnugat\WizardPlugin;

use Gnugat\Redaktilo\Editor;

class Kernel
{
    /**
     * @var Editor
     */
    private $editor;

    /**
     * @var string
     */
    private $filename;

    /**
     * @param Editor $editor
     * @param string $filename
     */
    public function __construct(Editor $editor, $filename)
    {
        $this->editor = $editor;
        $this->filename = $filename;
    }

    /**
     * @param string $bundleFqcn
     */
    public function registerBundle($bundleFqcn)
    {
        $bundleLine = "            new $bundleFqcn(),";

        $kernel = $this->editor->open($this->filename);
        if (!$this->editor->hasBelow($kernel, $bundleLine)) {
            return;
        }
        $this->editor->jumpBelow($kernel, '        );');
        $this->editor->insertAbove($kernel, $bundleLine);
        $this->editor->save($kernel);
    }
}
