<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins\OwnGlyphs;

use tiFy\Plugins\TinyMce\ExternalPlugins\AbstractExternalPluginGlyph;

class Ownglyphs extends AbstractExternalPluginGlyph
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        add_action('wp_ajax_tify_tinymce_external_plugins_ownglyphs', [$this, 'wp_ajax']);
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

        assets()->addInlineJs(
            "var glyphs=" . wp_json_encode($this->parseGlyphs()) . "," .
            "tinymceOwnGlyphsl10n={'title':'{$this->get('title')}'};",
            'admin'
        );

        assets()->addInlineCss(
            "i.mce-i-ownglyphs::before{content:'{$this->glyphs[$this->get('button')]}';}" .
            "i.mce-i-ownglyphs::before,.mce-grid a.ownglyphs{font-family:'{$this->get('font-family')}'!important;}",
            'admin'
        );
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        if ($this->get('editor_enqueue_scripts')) :
            $mce_css .= ', ' . $this->get('css');
        endif;

        return $mce_css .= ', ' . $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/editor.css, ' .
            admin_url(
                'admin-ajax.php?action=tify_tinymce_external_plugins_ownglyphs&bogus=' . current_time('timestamp')
            );
    }

    /**
     * Action Ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        header("Content-type: text/css");
        echo '.ownglyphs{font-family:' . $this->get('font-family') . ';}';
        exit;
    }
}