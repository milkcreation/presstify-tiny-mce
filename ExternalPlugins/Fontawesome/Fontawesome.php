<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins\FontAwesome;

use tiFy\Plugins\TinyMce\ExternalPlugins\AbstractExternalPluginGlyph;

class Fontawesome extends AbstractExternalPluginGlyph
{
    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'hookname'           => 'font-awesome',
            'css'                => home_url('/vendor/fortawesome/font-awesome/css/font-awesome.css'),
            'wp_enqueue_scripts' => true,
            'dependencies'       => [],
            'prefix'             => 'fa-',
            'font-family'        => 'fontAwesome',
            'button'             => 'flag',
            'title'              => __('Police de caractères fontAwesome', 'tify'),
            'cols'               => 32
        ];
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var fontAwesomeChars=<?php echo json_encode($this->parseGlyphs()); ?>,tinymceFontAwesomel10n={'title':'<?php echo $this->get('title'); ?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-fontawesome:before{content:"<?php echo $this->glyphs[$this->get('button')];?>";} i.mce-i-fontawesome:before,.mce-grid a.fontawesome{font-family:<?php echo $this->get('font-family'); ?>!important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.fontawesome{font-family:'<?php echo $this->get('font-family'); ?>';}</style><?php
    }
}