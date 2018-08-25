<?php

/**
 * @name TinyMce
 * @desc Extension PresstiFy de gestion l'interface d'administration de Wordpress.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package presstiFy
 * @namespace \tiFy\Plugins\TinyMce
 * @version 2.0.2
 */

namespace tiFy\Plugins\TinyMce;

use Illuminate\Support\Arr;
use tiFy\App\AppController;
use tiFy\Plugins\TinyMce\Plugins\Dashicons\Dashicons;
use tiFy\Plugins\TinyMce\Plugins\FontAwesome\FontAwesome;
use tiFy\Plugins\TinyMce\Plugins\Genericons\Genericons;
use tiFy\Plugins\TinyMce\Plugins\JumpLine\JumpLine;
use tiFy\Plugins\TinyMce\Plugins\OwnGlyphs\OwnGlyphs;
use tiFy\Plugins\TinyMce\Plugins\Table\Table;
use tiFy\Plugins\TinyMce\Plugins\Template\Template;
use tiFy\Plugins\TinyMce\Plugins\VisualBlocks\VisualBlocks;

final class TinyMce extends AppController
{
    /**
     * Liste des plugins disponibles.
     * @var array
     */
    protected $availablePlugins = [
        Dashicons::class,
        FontAwesome::class,
        Jumpline::class,
        OwnGlyphs::class,
        Table::class,
        Template::class,
        VisualBlocks::class
    ];

    /**
     * Liste des plugins déclarés.
     * @var array
     */
    protected $registeredPlugins = [];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        foreach($this->availablePlugins as $plugin) :
            $this->appServiceShare($plugin, new $plugin($this));
        endforeach;

        $this->appAddAction('init');
        $this->appAddFilter('tiny_mce_before_init');
        $this->appAddFilter('mce_external_plugins');
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        $this->setActivePlugins();
    }

    /**
     * Pré-filtrage des paramètres de configuration de tinyMce.
     *
     * @param array $mceInit Paramètres initiaux de tinyMce.
     *
     * @return array
     */
    final public function tiny_mce_before_init($mceInit)
    {
        if ($init = $this->appConfig('init', [])) :
            foreach ((array)$init as $key => $value) :
                switch ($key) :
                    default :
                        $mceInit[$key] = is_array($value)
                            ? json_encode($value)
                            : (string) $value;
                        break;
                    case 'toolbar' :
                        break;
                    case 'toolbar1' :
                    case 'toolbar2' :
                    case 'toolbar3' :
                    case 'toolbar4' :
                        $mceInit[$key] = $value;
                        $this->extractPluginsButtons($value);
                        break;
                endswitch;
            endforeach;
        endif;

        // Traitement des plugins externes
        foreach ($this->getActivePlugins() as $name => $attrs) :
            if (! $this->hasPluginButton($name)) :
                if (!empty($mceInit['toolbar3'])) :
                    $mceInit['toolbar3'] .= ' ' . $name;
                else :
                    $mceInit['toolbar3'] = $name;
                endif;
            endif;

            // Traitement de la configuration
            if (! $config = $this->getPluginConfig($name)) :
                continue;
            endif;

            foreach ($config as $key => $value) :
                if (isset($mceInit[$key])) :
                    continue;
                elseif (is_array($value)) :
                    $mceInit[$key] = json_encode($value);
                else :
                    $mceInit[$key] = (string) $value;
                endif;
            endforeach;
        endforeach;

        return $mceInit;
    }

    /**
     * Déclaration de la liste des plugins dans tinyMce.
     *
     * @param array $plugins Liste des url de plugin pré-définis dans tinyMce.
     *
     * @return string[]
     */
    final public function mce_external_plugins($plugins = [])
    {
        foreach ($this->getActivePlugins() as $name => $attrs) :
            if (! $url = $this->getPluginUrl($name)) :
                continue;
            endif;

            $plugins[$name] = $url;
        endforeach;

        return $plugins;
    }

    /**
     * Déclaration d'un plugin.
     *
     * @param string $name Identifiant de qualification du plugin.
     * @param string $url Url vers le plugin JS.
     *
     * @return bool
     */
    public function registerPlugin($name, $url)
    {
        Arr::set($this->registeredPlugins, $name, ['active' => false, 'url' => $url]);
    }

    /**
     * Vérification d'existance de déclaration d'un plugin.
     *
     * @param string $name Identifiant de qualification du plugin.
     *
     * @return bool
     */
    public function isRegisteredPlugin($name)
    {
        return in_array($name, array_keys($this->registeredPlugins));
    }

    /**
     * Définition de la liste des plugins actifs.
     *
     * @return void
     */
    public function setActivePlugins()
    {
        foreach ($this->appConfig('external_plugins', []) as $k => $v) :
            if (! $name = is_string($k) ? $k : (is_string($v) ? $v : '')) :
                continue;
            elseif ($this->isRegisteredPlugin($name)) :
                $this->registeredPlugins[$name]['active'] = true;
            endif;
        endforeach;
    }

    /**
     * Récupération de la liste des plugins actifs
     *
     * @return array
     */
    public function getActivePlugins()
    {
        return Arr::where(
            $this->registeredPlugins,
            function($row) {
                return ($row['active'] === true);
            }
        );
    }

    /**
     * Vérifie si un plugin est actif.
     *
     * @return bool
     */
    public function isActivePlugin($name)
    {
        return (bool)Arr::get($this->registeredPlugins, "{$name}.active", false);
    }

    /**
     * Récupération de l'url d'un plugin.
     *
     * @param string $name Identifiant de qualification du plugin.
     *
     * @return string
     */
    public function getPluginUrl($name)
    {
        return Arr::get($this->registeredPlugins, "{$name}.url", '');
    }

    /**
     * Définition des attributs de configuration d'un plugin.
     *
     * @param string $name Identifiant de qualification du plugin.
     * @param array $config Liste des attributs de configuration à définir
     *
     * @return void
     */
    public function setPluginConfig($name, $config)
    {
        if (did_action('tiny_mce_before_init')) :
            wp_die(__('La configuration des plugins tinyMce doit être appelé avant le filtre "tiny_mce_before_init"', 'tify'));
        endif;

        Arr::set($this->registeredPlugins, "{$name}.config", $config);
    }

    /**
     * Récupération de la configuration d'un plugin.
     *
     * @param string $name Identifiant de qualification du plugin.
     *
     * @return mixed
     */
    public function getPluginConfig($name)
    {
        return Arr::get($this->registeredPlugins, "{$name}.config", []);
    }

    /**
     * Extraction des boutons de plugin déclaré dans le fichier de configuration tinyMce.
     *
     * @param string $buttons Liste des boutons définis dans la configuration.
     *
     * @return void
     */
    public function extractPluginsButtons($buttons = '')
    {
        $_buttons = preg_split('#\||\s#', $buttons, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($_buttons as $name) :
            if ($this->isRegisteredPlugin($name)) :
                $this->registeredPlugins[$name]['button'] = true;
            endif;
        endforeach;
    }

    /**
     * Vérification d'existance d'un bouton déclaré
     *
     * @param string $name Identifiant de qualification du bouton.
     *
     * @return bool
     */
    public function hasPluginButton($name)
    {
        return (bool)Arr::get($this->registeredPlugins, "{$name}.button", false);
    }
}
