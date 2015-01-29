<?php

namespace Gnugat\WizardPlugin;

use Gnugat\Redaktilo\EditorFactory;

class Container
{
    /**
     * @var string
     */
    private $kernelFilename;

    /**
     * @param string $kernelFilename
     */
    public function __construct($kernelFilename)
    {
        $this->kernelFilename = $kernelFilename;
    }

    /**
     * @return PackageNameConvertor
     */
    public function getPackageNameConvertor()
    {
        return new PackageNameConvertor(
        );
    }

    /**
     * @return Kernel
     */
    public function getKernel()
    {
        return new Kernel(
            EditorFactory::createEditor(),
            $this->kernelFilename
        );
    }
}
