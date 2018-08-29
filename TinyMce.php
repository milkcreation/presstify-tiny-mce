<?php

/**
 * @name TinyMce
 * @desc Extension PresstiFy de gestion l'interface d'administration de Wordpress.
 * @author Jordy Manner <jordy@milkcreation.fr>
 * @package presstiFy
 * @namespace \tiFy\Plugins\TinyMce
 * @version 1.4.0
 */

namespace tiFy\Plugins\TinyMce;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use tiFy\App\Dependency\AbstractAppDependency;
use tiFy\Contracts\App\AppInterface;
use tiFy\Plugins\TinyMce\Contracts\ExternalPluginInterface;

/**
 * Class TinyMce
 * @package tiFy\Plugins\TinyMce
 *
 * Activation :
 * ----------------------------------------------------------------------------------------------------
 * Dans config/app.php ajouter \tiFy\Plugins\TinyMce\TinyMceServiceProvider à la liste des fournisseurs de services
 *     chargés automatiquement par l'application.
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
 * Configuration :
 * ----------------------------------------------------------------------------------------------------
 * Dans le dossier de config, créer le fichier social.php
 * @see /vendor/presstify-plugins/tiny-mce/Resources/config/tiny-mce.php Exemple de configuration
 */
final class TinyMce extends AbstractAppDependency
{
    /**
     * Liste des attributs de configuration complémentaires.
     * @var array
     */
    protected $additionnalConfig = [];

    /**
     * Liste des plugins externes déclarés.
     * @var ExternalPluginInterface[]
     */
    protected $externalPlugins = [];

    /**
     * Liste des boutons de plugin externes configuré dans la toolbar.
     * @var string[]
     */
    protected $toolbarButtons = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->appAddFilter(
            'mce_external_plugins',
            function ($externalPlugins = []) {
                foreach ($this->externalPlugins as $name => $plugin) :
                    $externalPlugins[$name] = $plugin->getUrl();
                endforeach;

                return $externalPlugins;
            }
        );

        $this->app->appAddFilter(
            'tiny_mce_before_init',
            function($mceInit)
            {
                foreach (config('tiny-mce.init') as $key => $value) :
                    switch ($key) :
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
                    endswitch;
                endforeach;

                foreach ($this->additionnalConfig as $key => $value) :
                    $mceInit[$key] = is_array($value) ? json_encode($value) : (string)$value;
                endforeach;

                foreach (array_keys($this->externalPlugins) as $name) :
                    if (!in_array($name, $this->toolbarButtons)) :
                        if (!empty($mceInit['toolbar3'])) :
                            $mceInit['toolbar3'] .= ' ' . $name;
                        else :
                            $mceInit['toolbar3'] = $name;
                        endif;
                    endif;
                endforeach;

                return $mceInit;
            }
        );
    }

    /**
     * Récupération du nom de la classe d'un plugin.
     *
     * @return string
     */
    public function getAbstract($alias)
    {
        $name = Str::studly($alias);
        $abstract = "tiFy\\Plugins\\TinyMce\\ExternalPlugins\\{$name}\\{$name}";

        if (class_exists($abstract)) :
            return $abstract;
        endif;

        return '';
    }

    /**
     * Récupération de l'instance de l'application.
     *
     * @return AppInterface
     */
    public function getApplication()
    {
        return $this->app;
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

        foreach ($exists as $name) :
            if (isset($this->externalPlugins[$name])) :
                $this->toolbarButtons[] = $name;
            endif;
        endforeach;
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
        $this->additionnalConfig = array_merge(
            $this->additionnalConfig,
            $attrs
        );

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
