<?php

namespace tiFy\Plugins\TinyMce\Plugins\JumpLine;

use tiFy\Plugins\TinyMce\Plugins\AbstractPlugin;

class JumpLine extends AbstractPlugin
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'jumpline';

    /**
     * Liste des options par défaut.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'wp_enqueue_style' => true
        ];
    }

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

        $this->appAddAction('admin_init');
        $this->appAddAction('wp_enqueue_scripts');
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) :
            $this->appAddAction('admin_head');
            $this->appAddAction('admin_print_styles');
            $this->appAddFilter('mce_css');
        endif;
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var tiFyTinyMCEJumpLinel10n={'title':'<?php _e('Saut de ligne', 'tify');?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-jumpline::before{content:"\f474";font-family:"dashicons";}</style><?php
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . self::tFyAppUrl(get_class()) . '/editor.css';
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        wp_enqueue_style('TinyMcePluginJumpLine', $this->appUrl() . '/theme.css', [], 160625);
    }
}