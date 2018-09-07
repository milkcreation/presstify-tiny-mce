<?php

namespace tiFy\Plugins\TinyMce;

use tiFy\App\Container\AppServiceProvider;
use tiFy\Plugins\TinyMce\TinyMce;
use tiFy\Plugins\TinyMce\Plugins\Dashicons\Dashicons;
use tiFy\Plugins\TinyMce\Plugins\FontAwesome\FontAwesome;
use tiFy\Plugins\TinyMce\Plugins\Genericons\Genericons;
use tiFy\Plugins\TinyMce\Plugins\JumpLine\JumpLine;
use tiFy\Plugins\TinyMce\Plugins\OwnGlyphs\OwnGlyphs;
use tiFy\Plugins\TinyMce\Plugins\Table\Table;
use tiFy\Plugins\TinyMce\Plugins\Template\Template;
use tiFy\Plugins\TinyMce\Plugins\VisualBlocks\VisualBlocks;

class TinyMceServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $singletons = [
        TinyMce::class
    ];

    /**
     * Liste des plugins actifs.
     * @var array
     */
    protected $externalPlugins = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $tinyMce = $this->app->resolve(TinyMce::class);

        $externalPlugins = config('tiny-mce.plugins', []);

        foreach ($externalPlugins as $name => $attrs) :
            if (is_numeric($name)) :
                $name = (string)$attrs;
                $attrs = [];
            endif;

            if (!$abstract = $tinyMce->getAbstract($name)) :
                continue;
            endif;

            $concrete = $this->app
                ->singleton($abstract)
                ->build([$name, $attrs, $tinyMce]);

            $this->externalPlugins[$name] = $concrete;
        endforeach;
    }
}