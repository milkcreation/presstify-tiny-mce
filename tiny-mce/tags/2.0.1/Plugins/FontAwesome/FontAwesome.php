<?php

namespace tiFy\Plugins\TinyMce\Plugins\FontAwesome;

use tiFy\tiFy;
use Composer\Autoload\ClassLoader;
use tiFy\Plugins\TinyMce\Plugins\AbstractPluginGlyph;

/**
 * Class FontAwesome
 * @package tiFy\Plugins\TinyMce\Plugins\FontAwesome
 *
 * @see https://gist.github.com/gubi/83402a9aae7cfa762df8
 */
class FontAwesome extends AbstractPluginGlyph
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'fontawesome';

    /**
     * Liste des attributs de configuration par défaut
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'hookname'         => 'font-awesome',
            'css'              => home_url('/vendor/fortawesome/font-awesome/css/font-awesome.css'),
            'wp_enqueue_style' => true,
            'dependencies'     => [],
            'prefix'           => 'fa-',
            'font-family'      => 'fontAwesome',
            'button'           => 'flag',
            'title'            => __('Police de caractères fontAwesome', 'tify'),
            'cols'             => 32
        ];
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var fontAwesomeChars=<?php echo json_encode($this->parseGlyphs()); ?>,tinymceFontAwesomel10n={'title':'<?php echo $this->getConfig('title'); ?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    final public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-fontawesome:before{content:"<?php echo $this->glyphs[$this->getConfig('button')];?>";} i.mce-i-fontawesome:before,.mce-grid a.fontawesome{font-family:<?php echo $this->getConfig('font-family'); ?>!important;}</style><?php
    }

    /**
     * Personnalisation de l'entête de l'interface utilisateur.
     *
     * @return void
     */
    final public function wp_head()
    {
?><style type="text/css">.fontawesome{font-family:'<?php echo $this->getConfig('font-family'); ?>';}</style><?php
    }
}