<?php declare(strict_types=1);

namespace tiFy\Plugins\TinyMce;

use tiFy\Container\ServiceProvider;
use tiFy\Plugins\TinyMce\{
    ExternalPlugins\Dashicons\Dashicons,
    ExternalPlugins\Fontawesome\Fontawesome,
    ExternalPlugins\Jumpline\Jumpline,
    ExternalPlugins\Ownglyphs\Ownglyphs,
    ExternalPlugins\Table\Table,
    ExternalPlugins\Template\Template,
    ExternalPlugins\Visualblocks\Visualblocks
};

class TinyMceServiceProvider extends ServiceProvider
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
     * @inheritDoc
     */
    public function boot(): void
    {
        add_action('after_setup_theme', function () {
            $this->getContainer()->get('tiny-mce');
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share('tiny-mce', function () {
            return new TinyMce($this->getContainer());
        });
        $this->registerPlugins();
    }

    /**
     * Déclaration des controleurs de plugins.
     *
     * @return void
     */
    public function registerPlugins(): void
    {
        $this->getContainer()->add('tiny-mce.plugins.dashicons', function ($name, $attrs) {
            return new Dashicons('dashicons', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.fontawesome', function ($name, $attrs) {
            return new Fontawesome('fontawesome', $attrs, $this->getContainer()->get('tiny-mce'));
        });

        $this->getContainer()->add('tiny-mce.plugins.jumpline', function ($name, $attrs) {
            return new Jumpline('jumpline', $attrs, $this->getContainer()->get('tiny-mce'));
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