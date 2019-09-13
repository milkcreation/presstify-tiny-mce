<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins;

use tiFy\Support\ParamsBag;
use tiFy\Plugins\TinyMce\Contracts\ExternalPluginInterface;
use tiFy\Plugins\TinyMce\TinyMce;

abstract class AbstractExternalPlugin extends ParamsBag implements ExternalPluginInterface
{
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

        $this->set($attrs)->parse();

        $this->tinyMce()->setExternalPlugin($this);

        $this->boot();
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {

    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->tinyMce()->getPluginUrl($this->getName());
    }

    /**
     * @inheritDoc
     */
    public function isActive()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function parse()
    {
        parent::parse();

        if ($mce_init = $this->get('mce_init', [])) {
            $this->tinyMce()->setAdditionnalConfig($mce_init);
        }
    }

    /**
     * @inheritDoc
     */
    public function tinyMce()
    {
        return $this->tinyMce;
    }
}