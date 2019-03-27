<?php

namespace tiFy\Plugins\TinyMce;

use Psr\Container\ContainerInterface;
use tiFy\Plugins\TinyMce\Contracts\ExternalPluginInterface;

/**
 * Class TinyMce
 *
 * @desc Extension PresstiFy de gestion de l'éditeur Wysiwyg TinyMCE.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package tiFy\Plugins\TinyMce
 * @version 2.0.9
 *
 * USAGE :
 * Activation
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\TinyMce\TinyMceServiceProvider à la liste des fournisseurs de services.
 * ex.
 * <?php
 * ...
 * use tiFy\Plugins\TinyMce\TinyMceServiceProvider;
 * ...
 *
 * return [
 *      ...
 *      'providers' => [
 *          ...
 *          TinyMceServiceProvider::class
 *          ...
 *      ]
 * ];
 *
 * Configuration
 * ---------------------------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier tiny-mce.php
 * @see /vendor/presstify-plugins/tiny-mce/Resources/config/tiny-mce.php
 */
final class TinyMce
{
    /**
     * Liste des attributs de configuration complémentaires.
     *
     * @var array
     */
    protected $additionnalConfig = [];

    /**
     * Conteneur d'injection de dépendances
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Liste des plugins externes déclarés.
     *
     * @var ExternalPluginInterface[]
     */
    protected $externalPlugins = [];

    /**
     * Liste des boutons de plugin externes configuré dans la toolbar.
     *
     * @var string[]
     */
    protected $toolbarButtons = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param ContainerInterface $container Conteneur d'injection de dépendances.
     *
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        add_action('init', function () {
            foreach (config('tiny-mce.plugins', []) as $name => $attrs) {
                if (is_numeric($name)) {
                    $name  = (string)$attrs;
                    $attrs = [];
                }

                if ($this->container->has("tiny-mce.plugins.{$name}")) {
                    $this->container->get("tiny-mce.plugins.{$name}", [$name, $attrs]);
                }
            }
        }, 0);


        add_filter('mce_external_plugins', function ($externalPlugins = []) {
            foreach ($this->externalPlugins as $name => $plugin) {
                $externalPlugins[$name] = $plugin->getUrl();
            }

            return $externalPlugins;
        });

        add_filter('tiny_mce_before_init', function ($mceInit) {
            foreach (config('tiny-mce.init', []) as $key => $value) {
                switch ($key) {
                    default :
                        $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
                        break;
                    case 'toolbar' :
                        break;
                    case 'toolbar1' :
                    case 'toolbar2' :
                    case 'toolbar3' :
                    case 'toolbar4' :
                        $mceInit[$key] = $value;
                        $this->getExternalPluginsButtons($value);
                        break;
                }
            }

            foreach ($this->additionnalConfig as $key => $value) {
                $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
            }

            foreach (array_keys($this->externalPlugins) as $name) {
                if (!in_array($name, $this->toolbarButtons)) {
                    if (!empty($mceInit['toolbar3'])) {
                        $mceInit['toolbar3'] .= ' ' . $name;
                    } else {
                        $mceInit['toolbar3'] = $name;
                    }
                }
            }

            return $mceInit;
        });
    }

    /**
     * Récupération de la liste des boutons de plugins externes déclarés dans la configuration.
     *
     * @param string $buttons Liste des boutons définis dans la configuration.
     *
     * @return void
     */
    public function getExternalPluginsButtons($buttons = '')
    {
        $exists = preg_split('#\||\s#', $buttons, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($exists as $name) {
            if (isset($this->externalPlugins[$name])) {
                $this->toolbarButtons[] = $name;
            }
        }
    }

    /**
     * Récupération de l'url vers les assets d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginAssetsUrl($name)
    {
        $cinfo = class_info($this);

        return (is_dir($cinfo->getDirname() . "/Resources/assets/plugins/{$name}"))
            ? $cinfo->getUrl() . "/Resources/assets/plugins/{$name}"
            : '';
    }

    /**
     * Récupération de l'url vers le scripts d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginUrl($name)
    {
        $cinfo = class_info($this);

        return (file_exists($cinfo->getDirname() . "/Resources/assets/plugins/{$name}/plugin.js"))
            ? $cinfo->getUrl() . "/Resources/assets/plugins/{$name}/plugin.js"
            : '';
    }

    /**
     * Définition des attributs de configuration additionnels.
     *
     * @param array $attrs Liste des attributs de configuration additionnels.
     *
     * @return $this
     */
    public function setAdditionnalConfig($attrs)
    {
        $this->additionnalConfig = array_merge($this->additionnalConfig, $attrs);

        return $this;
    }

    /**
     * Déclaration d'un plugin.
     *
     * @param ExternalPluginInterface $externalPlugin Instance de la classe du plugin externe.
     *
     * @return $this
     */
    public function setExternalPlugin(ExternalPluginInterface $externalPlugin)
    {
        $this->externalPlugins[$externalPlugin->getName()] = $externalPlugin;

        return $this;
    }
}
