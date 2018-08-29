<?php

namespace tiFy\Plugins\TinyMce\ExternalPlugins;

use Illuminate\Support\Collection;
use tiFy\Plugins\TinyMce\TinyMce;
use tiFy\Kernel\Tools;

abstract class AbstractExternalPluginGlyph extends AbstractExternalPlugin
{
    /**
     * Liste des attributs de configuration.
     * @var array {
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
    protected $attributes = [
        'hookname'           => 'dashicons',
        'css'                => '',
        'wp_enqueue_scripts' => true,
        'version'            => '',
        'dependencies'       => [],
        'prefix'             => 'dashicons-',
        'font-family'        => 'dashicons',
        'button'             => 'wordpress-alt',
        'title'              => '',
        'cols'               => 24
    ];

    /**
     * Liste des glyphs contenu dans la feuille de style de la police glyphs.
     * @var array
     */
    protected $glyphs = [];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app()->appAddAction('init', [$this, 'init']);
        $this->app()->appAddAction('admin_init', [$this, 'admin_init']);
        $this->app()->appAddAction('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        $this->app()->appAddAction('admin_head', [$this, 'admin_head']);
        $this->app()->appAddAction('admin_print_styles', [$this, 'admin_print_styles']);
        $this->app()->appAddAction('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'css'              => includes_url() . 'css/dashicons.css',
            'wp_enqueue_style' => true,
            'version'          => current_time('timestamp'),
            'title'            => __('Police de caractères Wordpress', 'tify')
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
            $this->app()->appAddFilter('mce_css', [$this, 'mce_css']);
        endif;
    }

    /**
     * Mise en file de scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        \wp_enqueue_style($this->get('hookname'));
        \wp_enqueue_style('tiFyTinyMceExternalPlugins' . class_info($this)->getShortName());
    }

    /**
     * Personnalisation de scripts de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_head()
    {
?><script type="text/javascript">/* <![CDATA[ */var dashiconsChars=<?php echo json_encode($this->parseGlyphs());?>,tinymceDashiconsl10n={'title':'<?php echo $this->get('title'); ?>'};/* ]]> */</script><?php
    }

    /**
     * Personnalisation de styles de l'entête de l'interface d'administration.
     *
     * @return string
     */
    public function admin_print_styles()
    {
?><style type="text/css">i.mce-i-dashicons:before{content:"<?php echo isset($this->glyphs[$this->get('button')]) ? $this->glyphs[$this->get('button')] : ''; ?>";} i.mce-i-dashicons:before,.mce-grid a.dashicons{font-family:<?php echo $this->get('font-family'); ?>!important;}</style><?php
    }

    /**
     * Initialisation globale de Wordpress.
     *
     * @return void
     */
    public function init()
    {
        \wp_register_style(
            $this->get('hookname'),
            $this->get('css'),
            $this->get('dependencies'),
            $this->get('version')
        );
        \wp_register_style(
            'tiFyTinyMceExternalPlugins' . class_info($this)->getShortName(),
            class_info($this)->getUrl() . '/plugin.css',
            [],
            $this->get('version')
        );

        $css = Tools::File()->getContents($this->get('css'));
        preg_match_all("#." . $this->get('prefix') . "(.*):before\s*\{\s*content\:\s*\"(.*)\";\s*\}\s*#", $css, $matches);
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
     * @return string
     */
    public function mce_css($mce_css)
    {
        return $mce_css .= ', ' . $this->get('css') . ', ' . class_info($this)->getUrl() . '/editor.css';
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

        $this->app()->appAssets()->addInlineCss(".{$this->name}{font-family:'{$this->get('font-family')}';}");
    }
}