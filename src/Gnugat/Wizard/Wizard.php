<?php

namespace Gnugat\Wizard;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Gnugat\Wizard\Command\ValidateConfigurationCommand;

class Wizard extends Application
{
    /**
     * Récupère le nom de la commande saisie.
     *
     * @param InputInterface $input L'interface de saisie
     *
     * @return string Le nom de la commande
     */
    protected function getCommandName(InputInterface $input)
    {
        // Retourne le nom de votre commande.
        return 'wizard:validate:configuration';
    }

    /**
     * Récupère les commandes par défaut qui sont toujours disponibles.
     *
     * @return array Un tableau d'instances de commandes par défaut
     */
    protected function getDefaultCommands()
    {
        // Conserve les commandes par défaut du noyau pour avoir la
        // commande HelpCommand en utilisant l'option --help
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new ValidateConfigurationCommand();

        return $defaultCommands;
    }

    /**
     * Surchargé afin que l'application accepte que le premier argument ne
     * soit pas le nom.
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // efface le premier argument, qui est le nom de la commande
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
