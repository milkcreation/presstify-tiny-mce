<?php

namespace tiFy\Plugins\TinyMce\Contracts;

use tiFy\Contracts\Kernel\ParametersBagInterface;
use tiFy\Plugins\TinyMce\TinyMce;

interface ExternalPluginInterface extends ParametersBagInterface
{
    /**
     * Initialisation du controleur de plugin
     *
     * @return void
     */
    public function boot();

    /**
     * Récupération de l'identifiant de qualification du plugin.
     *
     * @return string
     */
    public function getName();

    /**
     * Récupération de l'url vers le script JS du plugin.
     *
     * @return string
     */
    public function getUrl();

    /**
     * Vérification du status d'activité du plugin.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Récupération de l'instance de l'application.
     *
     * @return TinyMce
     */
    public function tinyMce();
}