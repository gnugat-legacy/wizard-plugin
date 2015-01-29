<?php

namespace Gnugat\WizardPlugin;

class PackageNameConvertor
{
    /**
     * @param string $packageName
     *
     * @return string
     */
    public function toFqcn($packageName)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./vendor/'.$packageName));
        $phpFiles = new RegexIterator($files, '/\.php$/');
        foreach ($phpFiles as $phpFile) {
            $tokens = token_get_all(file_get_contents($phpFile->getRealpath()));
            $totalTokens = count($tokens);
            for ($index = 0; $tokens < $totalTokens; $index++) {
                if (is_array($tokens[$index]) && T_EXTENDS === $tokens[$index][0]) {
                    if ('Bundle' === $tokens[$index + 1][1]) {
                        var_dump($packageName);
                    }
                }
            }
        }

        return 'Acme\\DemoBundle\\AcmeDemoBundle';
    }
}
