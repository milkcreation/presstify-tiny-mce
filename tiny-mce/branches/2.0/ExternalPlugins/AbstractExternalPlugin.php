<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins;

use tiFy\Kernel\Params\ParamsBag;
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

        parent::__construct($attrs);

        $this->tinyMce()->setExternalPlugin($this);

        add_action(
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
    public function boot()
    {

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
        return $this->tinyMce()->getPluginUrl($this->getName());
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
        parent::parse($attrs);

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