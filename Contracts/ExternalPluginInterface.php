<?php

namespace tiFy\Plugins\TinyMce\Contracts;

use tiFy\Contracts\App\AppInterface;
use tiFy\Plugins\TinyMce\TinyMce;

interface ExternalPluginInterface
{
    /**
     * Récupération de l'instance de l'application.
     *
     * @return AppInterface
     */
    public function app();

    /**
     * Initialisation du controleur de plugin
     *
     * @return void
     */
    public function boot();

    /**
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaults();

    /**
     * Récupération d'attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut. Syntaxe à point acceptée.
     * @param mixed $default Valeur de retour par defaut
     *
     * @return array
     */
    public function get($key, $default = null);

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
     * Traitement des attributs de configuration.
     *
     * @param array $attrs Liste des attributs de configuration personnalisés.
     *
     * @return array
     */
    public function parse($attrs = []);

    /**
     * Récupération de l'instance de l'application.
     *
     * @return TinyMce
     */
    public function tinyMce();
}