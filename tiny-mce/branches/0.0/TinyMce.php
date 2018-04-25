<?php

/**
 * @name TinyMce
 * @desc Extension PresstiFy de gestion l'interface d'administration de Wordpress.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package presstiFy
 * @namespace \tiFy\Plugins\AdminUi
 * @version 1.0.0
 */

namespace tiFy\Plugins\TinyMce;

use tiFy\App\Plugin;
use tiFy\Plugins\TinyMce\ExternalPlugins\Dashicons\Dashicons;
use tiFy\Plugins\TinyMce\ExternalPlugins\FontAwesome\FontAwesome;
use tiFy\Plugins\TinyMce\ExternalPlugins\Genericons\Genericons;
use tiFy\Plugins\TinyMce\ExternalPlugins\JumpLine\JumpLine;
use tiFy\Plugins\TinyMce\ExternalPlugins\OwnGlyphs\OwnGlyphs;
use tiFy\Plugins\TinyMce\ExternalPlugins\Table\Table;
use tiFy\Plugins\TinyMce\ExternalPlugins\Template\Template;
use tiFy\Plugins\TinyMce\ExternalPlugins\VisualBlocks\VisualBlocks;

class TinyMce extends Plugin
{
    // Liste des boutons actifs
    private static $Buttons = [];
    // Liste des url vers les plugins externes déclarés
    private static $RegistredExternalPluginsUrl = [];
    // Configuration des plugins externes déclarés
    private static $RegistredExternalPluginsConf = [];
    // Liste des plugins externes actifs
    private static $ExternalPluginsActive = [];
    // Liste des plugins externes actifs
    private static $ExternalPluginsConfig = [];

    /**
     * Liste des plugins disponibles.
     * @var array
     */
    protected $plugins = [
        'dashicons',
        'fontawesome',
        'genericons',
        'jumpline',
        'ownglyphs',
        'table',
        'template',
        'visualblocks'
    ];

    /* = CONSTRUCTEUR = */
    public function __construct()
    {
        parent::__construct();

        // Récupération de la configuration des plugins externe
        self::$ExternalPluginsConfig = $this->appConfig('external_plugins', [])
            ? : [];

        // Chargement des plugins
        new Dashicons();
        new FontAwesome();
        new Genericons();
        new JumpLine();
        new OwnGlyphs();
        new Table();
        new Template();
        new VisualBlocks();

        $this->appAddAction('tiny_mce_before_init');
        $this->appAddAction('mce_external_plugins');
    }

    /* = ACTIONS ET FILTRES WORDPRESS = */
    /** == Initialisation des paramètres de tinyMCE == **/
    final public function tiny_mce_before_init($mceInit)
    {
        // Traitement de la configuration personnalisée
        if ($init = self::tFyAppConfig('init')) :
            foreach ((array)$init as $key => $value) :
                switch ($key) :
                    default            :
                        if (is_array($value)) {
                            $mceInit[$key] = json_encode($value);
                        } elseif (is_string($value)) {
                            $mceInit[$key] = $value;
                        }
                        break;
                    case 'toolbar'    :
                        break;
                    case 'toolbar1'    :
                    case 'toolbar2'    :
                    case 'toolbar3'    :
                    case 'toolbar4'    :
                        $mceInit[$key] = $value;
                        $this->registerButtons(explode(' ', $value));
                        break;
                endswitch;
            endforeach;
        endif;

        // Traitement des plugins externes
        foreach ((array)$this->getExternalPluginsActive() as $name) :
            // Ajout des boutons de plugins non initiés dans la barre d'outil
            if (!in_array($name, self::$Buttons)) :
                if (!empty($mceInit['toolbar3'])) :
                    $mceInit['toolbar3'] .= ' ' . $name;
                else :
                    $mceInit['toolbar3'] = $name;
                endif;
            endif;

            // Traitement de la configuration
            if (isset(self::$RegistredExternalPluginsConf[$name])) :
                foreach ((array)self::$RegistredExternalPluginsConf[$name] as $key => $value) :
                    if (isset($mceInit[$key])) :
                        continue;
                    elseif (is_array($value)) :
                        $mceInit[$key] = json_encode($value);
                    elseif (is_string($value)) :
                        $mceInit[$key] = $value;
                    endif;
                endforeach;
            endif;
        endforeach;

        return $mceInit;
    }

    /** == Mise en file des plugins complémentaires == **/
    final public function mce_external_plugins($plugins = [])
    {
        foreach ($this->getExternalPluginsActive() as $name) :
            $plugins[$name] = self::$RegistredExternalPluginsUrl[$name];
        endforeach;

        return $plugins;
    }

    /* = CONTRÔLEUR = */
    /** == Déclaration de plugin externe == **/
    final static function registerExternalPlugin($name, $url, $config = [])
    {
        self::$RegistredExternalPluginsUrl[$name] = $url;

        if (!empty($config)) {
            self::$RegistredExternalPluginsConf[$name] = $config;
        }
    }

    /** == Récupération des plugins externes actifs == **/
    public function getExternalPluginsActive()
    {
        if (!empty(self::$ExternalPluginsActive)) {
            return self::$ExternalPluginsActive;
        }

        if (!self::tFyAppConfig('external_plugins')) {
            return [];
        }

        $plugins = [];
        foreach ((array)self::tFyAppConfig('external_plugins') as $k => $v) :
            $name = false;
            if (is_string($k)) {
                $name = $k;
            } elseif (is_string($v)) {
                $name = $v;
            }

            if ($name && in_array($name, array_keys(self::$RegistredExternalPluginsUrl)) && !in_array($name,
                    $plugins)) {
                array_push($plugins, $name);
            }
        endforeach;

        return self::$ExternalPluginsActive = $plugins;
    }

    /** == Récupération de la configuration d'un plugin déclaré == **/
    final static function getExternalPluginConfig($name)
    {
        if (isset(self::$ExternalPluginsConfig[$name])) {
            return self::$ExternalPluginsConfig[$name];
        }
    }

    /** == Déclaration des boutons == **/
    final static function registerButtons($buttons = [])
    {
        foreach ((array)$buttons as $button) :
            if (!in_array($button, self::$Buttons)) {
                array_push(self::$Buttons, $button);
            }
        endforeach;
    }
}