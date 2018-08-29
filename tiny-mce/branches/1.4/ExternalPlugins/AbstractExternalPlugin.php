<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins;

use Illuminate\Support\Arr;
use tiFy\Plugins\TinyMce\Contracts\ExternalPluginInterface;
use tiFy\Plugins\TinyMce\TinyMce;

abstract class AbstractExternalPlugin implements ExternalPluginInterface
{
    /**
     * Liste des attributs de configuration.
     * @var array
     */
    protected $attributes = [];

    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = '';

    /**
     * Instance du controleur principal.
     * @var TinyMce
     */
    protected $tinyMce;

    /**
     * CONSTRUCTEUR.
     *
     * @param string Nom de qualification du plugin.
     * @param array $attrs Liste des attributs de configuration.
     * @param TinyMce $tinyMce Instance du controleur principal.
     *
     * @return void
     */
    public function __construct($name, $attrs = [], TinyMce $tinyMce)
    {
        $this->name = $name;
        $this->tinyMce = $tinyMce;

        $this->tinyMce()->setExternalPlugin($this);

        $this->app()->appAddAction(
            'after_setup_theme',
            function () use ($attrs) {
                $this->parse($attrs);
            }
        );

        $this->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function app()
    {
        return $this->tinyMce()->getApplication();
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return class_info($this)->getUrl() . '/plugin.js';
    }

    /**
     * {@inheritdoc}
     */
    public function isActive()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        $this->attributes = array_merge(
            $this->attributes,
            $this->defaults(),
            $attrs
        );

        if ($mce_init = $this->get('mce_init', [])) :
            $this->tinyMce()->setAdditionnalConfig($mce_init);
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function tinyMce()
    {
        return $this->tinyMce;
    }
}