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

        $this->app()->appAddAction('wp_ajax_tify_tinymce_external_plugins_ownglyphs', [$this, 'wp_ajax']);
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var glyphs = <?php echo json_encode($this->parseGlyphs());?>, tinymceOwnGlyphsl10n = {'title': '<?php echo $this->get('title'); ?>'}; /* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-ownglyphs:before{content:"<?php echo $this->glyphs[$this->get('button')]; ?>";}i.mce-i-ownglyphs:before,.mce-grid a.ownglyphs{font-family: <?php echo $this->get('font-family'); ?> !important;}</style><?php
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->get('css') . ', ' . class_info($this)->getUrl() . '/editor.css, ' . admin_url
            ('admin-ajax.php?action=tify_tinymce_external_plugins_ownglyphs&bogus=' . current_time('timestamp'));
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