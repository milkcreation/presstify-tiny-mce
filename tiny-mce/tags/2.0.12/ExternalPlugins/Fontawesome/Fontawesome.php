<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins\FontAwesome;

use tiFy\Plugins\TinyMce\ExternalPlugins\AbstractExternalPluginGlyph;

class Fontawesome extends AbstractExternalPluginGlyph
{
    /**
     * @inheritdoc
     */
    public function defaults()
    {
        return array_merge(parent::defaults(), [
            'hookname'               => 'font-awesome',
            'css'                    => url()->root('/vendor/fortawesome/font-awesome/css/font-awesome.css'),
            'admin_enqueue_scripts'  => true,
            'editor_enqueue_scripts' => true,
            'wp_enqueue_scripts'     => true,
            'dependencies'           => [],
            'prefix'                 => 'fa-',
            'font-family'            => 'fontAwesome',
            'button'                 => 'flag',
            'title'                  => __('Police de caractères fontAwesome', 'tify'),
            'cols'                   => 32,
        ]);
    }

    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if ($this->get('admin_enqueue_scripts')) :
            wp_enqueue_style($this->get('hookname'));
        endif;

        wp_enqueue_style('tiFyTinyMceExternalPlugins' . class_info($this)->getShortName());

        asset()->setInlineJs(
            "let fontAwesomeChars=" . wp_json_encode($this->parseGlyphs()) .
            ",tinymceFontAwesomel10n={'title':'{$this->get('title')}'};",
            true
        );

        asset()->setInlineCss(
            "i.mce-i-fontawesome:before{content:'{$this->glyphs[$this->get('button')]}';}" .
            "i.mce-i-fontawesome:before,.mce-grid a.fontawesome{font-family:'{$this->get('font-family')}'!important;}"
        );
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_head()
    {
        asset()->setInlineCss(".fontawesome{font-family:'{$this->get('font-family')}';}");
    }
}