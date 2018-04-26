<?php

namespace tiFy\Plugins\TinyMce\Plugins;

use Illuminate\Support\Arr;
use tiFy\App\Traits\App as TraitsApp;
use tiFy\Plugins\TinyMce\TinyMce;

abstract class AbstractPlugin
{
    use TraitsApp;

    /**
     * Classe de rappel du plugin tinyMce
     * @var TinyMce
     */
    protected $tinyMce;

    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = '';

    /**
     * Liste des options de configuration.
     * @var array
     */
    protected $config;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(TinyMce $tinyMce)
    {
        $this->tinyMce = $tinyMce;

        // Déclaration du plugin
        if (file_exists($this->appDirname())) :
            $this->tinyMce->registerPlugin($this->name, $this->appUrl() . '/plugin.js');
        endif;

        $this->appAddAction('init', null, 0);
    }

    /**
     * Vérification du status d'activité du plugin
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->tinyMce->isActivePlugin($this->getName());
    }

    /**
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [];
    }

    /**
     * Définition de la configuration.
     *
     * @return array
     */
    public function setConfig()
    {
        // Traitement de la configuration.
        $this->config = array_merge(
            $this->defaultConfig(),
            $this->tinyMce->appConfig(
                'external_plugins.' . $this->getName(),
                []
            )
        );

        if ($mce_init = $this->getConfig('mce_init')) :
            $this->tinyMce->setPluginConfig($this->getName(), $mce_init);
        endif;
    }

    /**
     * Récupération d'attribut de configuration.
     *
     * @param string $key Clé d'index de l'attribut. Syntaxe à point acceptée.
     * @param mixed $default Valeur de retour par defaut
     *
     * @return array
     */
    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * Récupération de l'identifiant de qualification du plugin.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    public function init()
    {
        if (! $this->isActive()) :
            return;
        endif;

        $this->setConfig();
    }

}