<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins\JumpLine;

use tiFy\Plugins\TinyMce\ExternalPlugins\AbstractExternalPlugin;

class JumpLine extends AbstractExternalPlugin
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app()->appAddAction('admin_init', [$this, 'admin_init']);
        $this->app()->appAddAction('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'wp_enqueue_scripts' => true
        ];
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) :
            $this->app()->appAddAction('admin_head', [$this, 'admin_head']);
            $this->app()->appAddAction('admin_print_styles', [$this, 'admin_print_styles']);
            $this->app()->appAddFilter('mce_css', [$this, 'mce_css']);
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
        return $mce_css .= ', ' . class_info($this)->getUrl() . '/editor.css';
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if ($this->get('wp_enqueue_scripts') && $this->isActive()) :
            wp_enqueue_style('TinyMceExternalPluginsJumpLine', class_info($this)->getUrl() . '/theme.css', [], 160625);
        endif;
    }
}