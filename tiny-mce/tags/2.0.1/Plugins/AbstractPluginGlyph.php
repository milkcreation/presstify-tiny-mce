<?php

namespace tiFy\Plugins\TinyMce\Plugins;

use Illuminate\Support\Collection;
use tiFy\Plugins\TinyMce\TinyMce;
use tiFy\Kernel\Tools;

abstract class AbstractPluginGlyph extends AbstractPlugin
{
    /**
     * Liste des attributs de configuration.
     * @var array {
     *
     *      @var string $hookname Nom d'accroche pour la mise en file de la police de caractères.
     *      @var string $css Url vers la police CSS. La police doit être non minifiée.
     *      @var bool $wp_enqueue_style Activation de la mise en file automatique de la feuille de style de la police de caractères.
     *      @var string $version Numéro de version utilisé lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
     *      @var array $dependencies Liste des dépendances lors de la mise en file de la feuille de style de la police de caractères. La mise en file auto doit être activée.
     *      @var string $prefix Préfixe des classes de la police de caractères.
     *      @var string $font -family Nom d'appel de la Famille de la police de caractères.
     *      @var string $title Intitulé de l'infobulle du bouton et titre de la boîte de dialogue.
     *      @var string $button Nom du glyph utilisé pour illustré le bouton de l'éditeur TinyMCE.
     *      @var int $cols Nombre d'éléments affichés dans la fenêtre de selection de glyph du plugin TinyMCE.
     * }
     */
    protected $config;

    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    protected $glyphs = [];

    /**
     * Liste des attributs de configuration par défaut
     *
     * @return array
     */
    public function defaultConfig()
    {
        return [
            'hookname'         => 'dashicons',
            'css'              => includes_url() . 'css/dashicons.css',
            'wp_enqueue_style' => true,
            'version'          => current_time('timestamp'),
            'dependencies'     => [],
            'prefix'           => 'dashicons-',
            'font-family'      => 'dashicons',
            'button'           => 'wordpress-alt',
            'title'            => __('Police de caractères Wordpress', 'tify'),
            'cols'             => 24
        ];
    }

    /**
     * Initialisation globale.
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        $this->appAddAction('admin_init');
        $this->appAddAction('admin_enqueue_scripts');
        $this->appAddAction('admin_head');
        $this->appAddAction('admin_print_styles');
        $this->appAddAction('wp_enqueue_scripts');
        $this->appAddAction('wp_ajax_' . $this->name, [$this, 'wp_ajax']);

        // Déclaration des scripts.
        \wp_register_style(
            $this->getConfig('hookname'),
            $this->getConfig('css'),
            $this->getConfig('dependencies'),
            $this->getConfig('version')
        );
        \wp_register_style(
            'tiFyPluginTinyMce-' . $this->appShortname(),
            $this->appUrl() . '/plugin.css',
            [],
            $this->getConfig('version')
        );

        // Traitement de la listes des glyphs dans la feuille de style de la police de caractères
        $css = Tools::File()->getContents($this->getConfig('css'));

        preg_match_all("#." . $this->getConfig('prefix') . "(.*):before\s*\{\s*content\:\s*\"(.*)\";\s*\}\s*#", $css, $matches);

        if (isset($matches[1])) :
            foreach ($matches[1] as $i => $class) :
                if(isset($matches[2][$i])) :
                    $this->glyphs[$class] = $matches[2][$i];
                endif;
            endforeach;
        endif;
    }

    /**
     * Initialisation de l'interface d'administration.
     *
     * @return void
     */
    public function admin_init()
    {
        if ((current_user_can('edit_posts') || current_user_can('edit_pages')) && get_user_option('rich_editing')) :
            $this->appAddFilter('mce_css');
        endif;
    }

    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style($this->getConfig('hookname'));
        \wp_enqueue_style('tiFyPluginTinyMce-' . $this->appShortname());
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var dashiconsChars=<?php echo json_encode($this->parseGlyphs());?>,tinymceDashiconsl10n={'title':'<?php echo $this->getConfig('title'); ?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-dashicons:before{content:"<?php echo isset($this->glyphs[$this->getConfig('button')]) ? $this->glyphs[$this->getConfig('button')] : ''; ?>";} i.mce-i-dashicons:before,.mce-grid a.dashicons{font-family:<?php echo $this->getConfig('font-family'); ?>!important;}</style><?php
    }

    /**
     * Ajout de styles dans l'éditeur tinyMCE.
     *
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->getConfig('css') . ', ' . $this->appUrl() . '/editor.css';
    }

    /**
     * Mise en file de scripts de l'interface utilisateur.
     *
     * @return void
     */
    public function wp_enqueue_scripts()
    {
        if ($this->getConfig('wp_enqueue_style') && $this->isActive()) :
            wp_enqueue_style($this->getConfig('hookname'));
        endif;

        $this->appAssets()->addInlineCss(".{$this->name}{font-family:'{$this->getConfig('font-family')}';}");
    }

    /**
     * Traitement de récupération des glyphs depuis le fichier CSS
     */
    public function parseGlyphs()
    {
        $items = array_map(
            function($value){
                return preg_replace('#' . preg_quote('\\') . '#', '&#x', $value);
            },
            $this->glyphs
        );
        $collection = new Collection($items);

        return $collection->chunk($this->getConfig('cols'))->toArray();
    }
}