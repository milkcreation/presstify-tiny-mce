<?php

namespace tiFy\Plugins\TinyMce\Plugins\OwnGlyphs;

use tiFy\Plugins\TinyMce\Plugins\AbstractPluginGlyph;

class OwnGlyphs extends AbstractPluginGlyph
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'ownglyphs';

    /**
     * Initialisation globale.
     *
     * @return void
     */
    public function init()
    {
        if (! $this->isActive()) :
            return;
        endif;

        parent::init();

        $this->appAddAction('wp_ajax_tinymce-ownglyphs-class', [$this, 'wp_ajax']);
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var glyphs = <?php echo $this->parseGlyphs();?>, tinymceOwnGlyphsl10n = {'title': '<?php echo $this->getConfig('title'); ?>'}; /* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-ownglyphs:before{content:"<?php echo $this->glyphs[$this->getConfig('button')]; ?>";}i.mce-i-ownglyphs:before,.mce-grid a.ownglyphs{font-family: <?php echo $this->getConfig('font-family'); ?> !important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.ownglyphs{font-family:'<?php echo $this->getConfig('font-family'); ?>';}</style><?php
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    final public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->getConfig('css') . ', ' . $this->appUrl() . '/editor.css, ' . admin_url('admin-ajax.php?action=tinymce-ownglyphs-class&bogus=' . current_time('timestamp'));
    }

    /**
     * Action Ajax.
     *
     * @return string
     */
    final public function wp_ajax()
    {
        header("Content-type: text/css");
        echo '.ownglyphs{font-family:' . $this->getConfig('font-family') . ';}';
        exit;
    }
}