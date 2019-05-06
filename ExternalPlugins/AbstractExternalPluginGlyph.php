<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins;

use Illuminate\Support\Collection;
use tiFy\Kernel\Tools;

abstract class AbstractExternalPluginGlyph extends AbstractExternalPlugin
{
    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    protected $glyphs = [];

    /**
     * @inheritdoc
     */
    public function boot()
    {
        add_action('admin_init', [$this, 'admin_init']);
        add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /**
     * Liste des attributs de configuration.
     * @return array {
     *      @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
     *      @var string $css Url vers la police CSS. La police doit être non minifiée.
     *      @var bool $editor_enqueue_style Activation de la mise en file automatique de la feuille de style de la
     *                                      police de caractères dans l'éditeur.
     *      @var bool $admin_enqueue_style Activation de la mise en file automatique de la feuille de style de la
     *                                     police de caractères dans l'interface d'administration (bouton).
     *      @var bool $wp_enqueue_style Activation de la mise en file automatique de la feuille de style de la police
     *                                  de caractères.
     *      @var string $version Numéro de version utilisé lors de la mise en file de la feuille de style de la police
     *                           de caractères. La mise en file auto doit être activée.
     *      @var array $dependencies Liste des dépendances lors de la mise en file de la feuille de style de la police
     *                               de caractères. La mise en file auto doit être activée.
     *      @var string $prefix Préfixe des classes de la police de caractères.
     *      @var string $font -family Nom d'appel de la Famille de la police de caractères.
     *      @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
     *      @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
     *      @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
     * }
     */
    public function defaults()
    {
        return [
            'hookname'               => 'dashicons',
            'css'                    => includes_url() . 'css/dashicons.css',
            'admin_enqueue_scripts'  => true,
            'editor_enqueue_scripts' => true,
            'wp_enqueue_scripts'     => true,
            'version'                => current_time('timestamp'),
            'dependencies'           => [],
            'prefix'                 => 'dashicons-',
            'font-family'            => 'dashicons',
            'button'                 => 'wordpress-alt',
            'title'                  => __('Police de caractères Wordpress', 'tify'),
            'cols'                   => 24
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
            add_filter('mce_css', [$this, 'mce_css']);
        endif;
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
            "let dashiconsChars=" . json_encode($this->parseGlyphs()) .
            ",tinymceDashiconsl10n={'title':'{$this->get('title')}'};",
            true
        );

        asset()->setInlineCss(
            "i.mce-i-dashicons:before{" .
            "content:'" . ($this->glyphs[$this->get('button')] ? $this->glyphs[$this->get('button')] : '') . "';}".
            "i.mce-i-dashicons:before,.mce-grid a.dashicons{font-family:'{$this->get('font-family')}'!important;}"
        );
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        wp_register_style(
            $this->get('hookname'),
            $this->get('css'),
            $this->get('dependencies'),
            $this->get('version')
        );
        wp_register_style(
            'tiFyTinyMceExternalPlugins' . class_info($this)->getShortName(),
            $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/plugin.css',
            [],
            $this->get('version')
        );

        $css = Tools::File()->getContents($this->get('css'));
        preg_match_all(
            "#." . $this->get('prefix') . "(.*):before\s*\{\s*content\:\s*\"(.*)\";\s*\}\s*#",
            $css,
            $matches
        );
        if (isset($matches[1])) :
            foreach ($matches[1] as $i => $class) :
                if(isset($matches[2][$i])) :
                    $this->glyphs[$class] = $matches[2][$i];
                endif;
            endforeach;
        endif;
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @param string $mce_css Liste des url vers les feuilles de styles associées à tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        if ($this->get('editor_enqueue_scripts')) :
            $mce_css .= ', ' . $this->get('css');
        endif;

        return $mce_css . ', ' . $this->tinyMce()->getPluginAssetsUrl($this->getName()) . '/css/editor.css';
    }

    /**
     * Traitement de récupération des glyphs depuis le fichier CSS.
     *
     * @return array
     */
    public function parseGlyphs()
    {
        $items = array_map(function($value){
                return preg_replace('#' . preg_quote('\\') . '#', '&#x', $value);
            },
            $this->glyphs
        );
        $collection = new Collection($items);

        return $collection->chunk($this->get('cols'))->toArray();
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if ($this->get('wp_enqueue_scripts') && $this->isActive()) :
            wp_enqueue_style($this->get('hookname'));
        endif;

        asset()->setInlineCss(".{$this->name}{font-family:'{$this->get('font-family')}';}");
    }
}