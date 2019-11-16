<?php

/**
 * Exemple de configuration.
 */

return [
    /**
     * Attributs de configuration de tinyMCE.
     * @see https://www.tiny.cloud/docs/configure/
     */
    'init'    => [
        /**
         * Personnalisation de la première ligne de la barre d'outils.
         */
        'toolbar1' => 'bold italic underline strikethrough blockquote ' .
            '| alignleft aligncenter alignright alignjustify | bullist numlist outdent indent ' .
            '| . link unlink hr | dfw wp_adv',

        /**
         * Personnalisation de la deuxième ligne de la barre d'outils.
         */
        'toolbar2' => 'undo redo | pastetext | styleselect formatselect ' .
            '| fontselect fontsizeselect forecolor backcolor | removeformat | subscript superscript charmap',

        'textcolor_map' => [
            '000000',
            __('Noir', 'tify'),
            'FFFFFF',
            __('Blanc', 'tify'),
            '848484',
            __('Police standard', 'tify')
        ],

        'block_formats' => 'Paragraphe=p;Paragraphe sans espace=div;Titre 3=h3;Titre 4=h4;Titre 5=h5;Titre 6=h6',

        'font_formats' => 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;',

        'fontsize_formats' => '10px 11px 12px 13px 14px 16px 18px 20px 24px 28px 32px 36px 40px 44px 48px 52px 64px '.
            '128px 256px',

        'style_formats' => [
            [
                'title'    => __('Alignement à  Gauche', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignleft'
            ],
            [
                'title'    => __('Alignement au Centre', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'aligncenter'
            ],
            [
                'title'    => __('Alignement à  Droite', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignright'
            ],
            [
                'title'    => __('Alignement vertical en haut', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'aligntop'
            ],
            [
                'title'    => __('Alignement vertical au milieu', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignmiddle'
            ],
            [
                'title'    => __('Alignement vertical en bas', 'tify'),
                'selector' => 'p, span, img, a',
                'classes'  => 'alignbottom'
            ],
            [
                'title'   => __('Texte en majuscules', 'tify'),
                'inline'  => 'span',
                'classes' => 'uppercase'
            ],
            [
                'title'    => __('Bouton 1', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--1'
            ],
            [
                'title'    => __('Bouton 1 inversé', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--1 Button--inverted'
            ],
            [
                'title'    => __('Bouton 2', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--2'
            ],
            [
                'title'    => __('Bouton 2 inversé', 'tify'),
                'selector' => 'a',
                'classes'  => 'Button--2 Button--inverted'
            ],
            [
                'title'    => __('Etiquette 1', 'tify'),
                'selector' => 'a',
                'classes'  => 'Label--1'
            ],
            [
                'title'    => __('Etiquette 1 inversée', 'tify'),
                'selector' => 'a',
                'classes'  => 'Label--1 Label--inverted'
            ],
            [
                'title'    => __('Etiquette 2', 'tify'),
                'selector' => 'a',
                'classes'  => 'Label--2'
            ],
            [
                'title'    => __('Etiquette 2 inversée', 'tify'),
                'selector' => 'a',
                'classes'  => 'Label--2 Label--inverted'
            ]
        ]
    ],
    'plugins' => [
        /**
         * Intégration de la police de glyphs Wordpress (dashicons).
         * @see https://developer.wordpress.org/resource/dashicons
         */
        'dashicons' => [
            /**
             * Désactivation du chargement automatique des scripts.
             * {@internal Ajouter "import 'wp-css/dashicons.css';" à votre feuille de style global}
             */
            'wp_enqueue_scripts' => false,
        ],

        /**
         * Intégration de la police de glyphs FontAwesome.
         * @see https://fontawesome.com/icons?d=gallery
         */
        'fontawesome' => [
            /**
             * Désactivation du chargement automatique des scripts.
             * {@internal Ajouter "import '@/vendor/fortawesome/font-awesome/css/font-awesome.css';" à votre feuille de style global}
             */
            'wp_enqueue_scripts' => false,
        ],

        /**
         * Activation du saut de ligne automatique.
         */
        'jumpline' => [
            /**
             * Désactivation du chargement automatique des scripts.
             * {@internal Ajouter "import 'presstify-plugins/tiny-mce/ExternalPlugins/Jumpline/theme.css';" à votre feuille de style global}
             */
            'wp_enqueue_scripts' => false,
        ],

        /**
         * Intégration d'une police de glyphs personnelle.
         * @see http://fontastic.me/
         * @see http://fontello.com/
         * @see https://icomoon.io/app/#/select
         */
        'ownglyphs' => [
            'hookname'           => 'tify',
            'css'                => get_stylesheet_directory_uri() . '/src/fonts/presstify.com/styles.css',
            'version'            => '1.0',
            'dependencies'       => [],
            'prefix'             => 'tify-',
            'font-family'        => 'tify',
            'button'             => 'logo',
            'title'              => __('Police de glyphs presstiFy', 'tify'),
            'cols'               => 8,
            /**
             * Désactivation du chargement automatique des scripts.
             * {@internal Ajouter "import 'src/fonts/sedea-pro.fr/styles.css';" à votre feuille de style global}
             */
            'wp_enqueue_scripts' => false,
        ],

        /**
         * Gestion des tableaux.
         * @see https://www.tiny.cloud/docs/plugins/table/
         */
        'table',

        /**
         * Gabarits d'affichage préformatés.
         * @see https://www.tiny.cloud/docs/plugins/template/
         */
        'template' => [
            /**
             * Désactivation du chargement automatique des scripts.
             * {@internal Ajouter "import 'presstify-plugins/tiny-mce/ExternalPlugins/Template/theme.css';" à votre feuille de style global}
             */
            'wp_enqueue_scripts' => false,
        ],

        /**
         * Asistance visuel d'affichage des blocs de saisie.
         * @see https://www.tiny.cloud/docs/plugins/visualblocks/
         */
        'visualblocks'
    ]
];