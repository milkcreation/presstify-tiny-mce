<?php

namespace tiFy\Plugins\TinyMce\Plugins\Genericons;

use tiFy\Plugins\TinyMce\Plugins\AbstractPluginGlyph;

class Genericons extends AbstractPluginGlyph
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'genericons';

    /**
     * Liste des options par défaut.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'hookname'         => 'genericons',
            'css'              => \tify_style_get_src('genericons', 'dev'),
            'wp_enqueue_style' => true,
            'version'          => '4.5.0',
            'dependencies'     => [],
            'prefix'           => 'genericon-',
            'font-family'      => 'Genericons',
            'button'           => 'aside',
            'title'            => __('Police de caractères Genericons', 'tify'),
            'cols'             => 16
        ];
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var GenericonsChars =<?php echo $this->parseGlyphs();?>,tinymceGenericonsl10n={'title':'<?php echo $this->getConfig('title'); ?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-genericons:before{content:"<?php echo $this->glyphs[$this->getConfig('button')]; ?>";}i.mce-i-genericons:before,.mce-grid a.genericons{font-family:<?php echo $this->getConfig('font-family'); ?>!important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.genericons{font-family: '<?php echo $this->getConfig('font-family'); ?>';}</style><?php
    }
}