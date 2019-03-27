<?php

namespace tiFy\Plugins\TinyMce;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Plugins\TinyMce\ExternalPlugins\Dashicons\Dashicons;
use tiFy\Plugins\TinyMce\ExternalPlugins\Fontawesome\Fontawesome;
use tiFy\Plugins\TinyMce\ExternalPlugins\JumpLine\JumpLine;
use tiFy\Plugins\TinyMce\ExternalPlugins\Ownglyphs\Ownglyphs;
use tiFy\Plugins\TinyMce\ExternalPlugins\Table\Table;
use tiFy\Plugins\TinyMce\ExternalPlugins\Template\Template;
use tiFy\Plugins\TinyMce\ExternalPlugins\Visualblocks\Visualblocks;

class TinyMceServiceProvider extends AppServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        'tiny-mce',
        'tiny-mce.plugins.dashicons',
        'tiny-mce.plugins.fontawesome',
        'tiny-mce.plugins.jumpline',
        'tiny-mce.plugins.ownglyphs',
        'tiny-mce.plugins.table',
        'tiny-mce.plugins.template',
        'tiny-mce.plugins.visualblocks'
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        add_action('after_setup_theme', function () {
            $this->getContainer()->get('tiny-mce');
        });
    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->add('tiny-mce', function () {
            return new TinyMce($this->getContainer());
        });

        $this->registerPlugins();
    }

    /**
     * Déclaration des controleurs de plugins.
     *
     * @return void
     */
    public function registerPlugins()
    {
        $this->getContainer()->add('tiny-mce.plugins.dashicons', function ($name, $attrs) {
            return new Dashicons('dashicons', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.fontawesome', function ($name, $attrs) {
            return new FontAwesome('fontawesome', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.jumpline', function ($name, $attrs) {
            return new JumpLine('jumpline', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.ownglyphs', function ($name, $attrs) {
            return new Ownglyphs('ownglyphs', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.table', function ($name, $attrs) {
            return new Table('table', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.template', function ($name, $attrs) {
            return new Template('template', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.visualblocks', function ($name, $attrs) {
            return new Visualblocks('visualblocks', $attrs, $this->getContainer()->get('tiny-mce'));
        });
    }
}