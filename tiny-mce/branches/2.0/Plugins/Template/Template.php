<?php

namespace tiFy\Plugins\TinyMce\Plugins\Template;

use tiFy\Plugins\TinyMce\Plugins\AbstractPlugin;

class Template extends AbstractPlugin
{
    /**
     * Nom de qualification du plugin.
     * @var string
     */
    protected $name = 'template';

    /**
     * Liste des attributs de configuration par défaut.
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'wp_enqueue_style' => true,
            'mce_init'         => [
                'templates' => \add_query_arg(
                    [
                        'action' => 'tinymce_plugin_template',
                        'nonce'  => \wp_create_nonce('TinyMcePluginTemplate')
                    ],
                    admin_url('admin-ajax.php')
                )
            ]
        ];
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->appAddFilter('mce_css');
        $this->appAddAction('wp_enqueue_scripts');
        $this->appAddAction('wp_ajax_tinymce_plugin_template', [$this, 'wp_ajax']);
    }

    /**
     * Action ajax.
     *
     * @return string
     */
    public function wp_ajax()
    {
        if (!wp_verify_nonce($_GET['nonce'], 'TinyMcePluginTemplate')) :
            return;
        endif;

        nocache_headers();

        header('Content-Type: application/x-javascript; charset=UTF-8');

        echo json_encode([
            [
                "title"       => "2 Colonnes : 1/4, 3/4",
                "description" => "1 colonne d'1/4 et l'autre de 3/4",
                "url"         => $this->appUrl() . "/templates/2cols_0.25-0.75.htm"
            ],
            [
                "title"       => "2 Colonnes : 1/3, 2/3",
                "description" => "1 colonne d'1/3 et l'autre de 2/3",
                "url"         => $this->appUrl() . "/templates/2cols_0.33-0.66.htm"
            ],
            [
                "title"       => "2 Colonnes : 1/2, 1/2",
                "description" => "1 colonnes d'1/2 et l'autre d'1/2",
                "url"         => $this->appUrl() . "/templates/2cols_0.5-0.5.htm"
            ],
            [
                "title"       => "2 Colonnes : 2/3, 1/3",
                "description" => "1 colonne de 2/3 et l'autre d'1/3",
                "url"         => $this->appUrl() . "/templates/2cols_0.66-0.33.htm"
            ],
            [
                "title"       => "2 Colonnes : 3/4, 1/4",
                "description" => "1 colonne de 3/4 et l'autre d'1/4",
                "url"         => $this->appUrl() . "/templates/2cols_0.75-0.25.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/4, 1/4, 1/2",
                "description" => "1 colonne d'1/4, une d'1/4 et une d'1/2",
                "url"         => $this->appUrl() . "/templates/3cols_0.25-0.25-0.5.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/4, 1/2, 1/4",
                "description" => "1 colonne d'1/4, une d'1/2 et une d'1/4",
                "url"         => $this->appUrl() . "/templates/3cols_0.25-0.5-0.25.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/3, 1/3, 1/3",
                "description" => "1 colonne d'1/3, une d'1/3 et une d'1/3",
                "url"         => $this->appUrl() . "/templates/3cols_0.33-0.33-0.33.htm"
            ],
            [
                "title"       => "3 Colonnes : 1/2, 1/4, 1/4",
                "description" => "1 colonne d'1/2, une d'1/4 et une d'1/4",
                "url"         => $this->appUrl() . "/templates/3cols_0.5-0.25-0.25.htm"
            ],
            [
                "title"       => "4 Colonnes : 1/4, 1/4, 1/4, 1/4",
                "description" => "1 colonnes d'1/4, une d'1/4, une d'1/4 et une d'1/4",
                "url"         => $this->appUrl() . "/templates/4cols_0.25-0.25-0.25-0.25.htm"
            ]
        ]);
        exit;
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->appUrl() . '/editor.css';
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if ($this->getConfig('wp_enqueue_style') && $this->isActive()) :
            wp_enqueue_style('TinyMcePluginTemplate', $this->appUrl() . '/theme.css', [], 150317);
        endif;
    }
}