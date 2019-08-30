<?php declare(strict_types=1);

namespace tiFy\Plugins\TinyMce;

use tiFy\Contracts\Field\FieldFactory as FieldFactoryContract;
use tiFy\Field\{FieldFactory, FieldView};
use tiFy\Plugins\TinyMce\Contracts\{TinyMce, TinyMceField as TinyMceFieldContract};
use tiFy\Support\Str;

class TinyMceField extends FieldFactory implements TinyMceFieldContract
{
    /**
     * Instance du gestionnaire du plugin associé.
     * @var TinyMce
     */
    protected $plugin;

    /**
     * {@inheritDoc}
     *
     * @return array {
     * @var array $attrs Attributs HTML du champ.
     * @var string $after Contenu placé après le champ.
     * @var string $before Contenu placé avant le champ.
     * @var string $name Clé d'indice de la valeur de soumission du champ.
     * @var string $value Valeur courante de soumission du champ.
     * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
     * @var string $content Contenu de la balise HTML.
     * @var string $type Type de bouton. button par défaut.
     * }
     */
    public function defaults(): array
    {
        return [
            'attrs'   => [],
            'after'   => '',
            'before'  => '',
            'name'    => '',
            'value'   => '',
            'viewer'  => [],
            'options' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function parse(): FieldFactoryContract
    {
        parent::parse();

        $options = array_merge([
            'content_css' => [],
            'skin'        => false,
        ], $this->get('options', []));

        $this->set([
            'attrs.data-control' => 'tinymce',
            'attrs.data-options' => $options,
        ]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function parseDefaults(): FieldFactoryContract
    {
        $default_class = 'Field-' . Str::camel($this->getAlias()) .
            ' Field-' . Str::camel($this->getAlias()) . '--' . $this->getIndex();
        if (!$this->has('attrs.class')) {
            $this->set('attrs.class', $default_class);
        } else {
            $this->set('attrs.class', sprintf($this->get('attrs.class', ''), $default_class));
        }

        $this->parseName();
        $this->parseViewer();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function viewer($view = null, $data = [])
    {
        if (!$this->viewer) {
            $default_dir = $this->plugin->resourcesDir('/views/field');
            $this->viewer = view()
                ->setDirectory($default_dir)
                ->setController(FieldView::class)
                ->setOverrideDir(
                    (($override_dir = $this->get('viewer.override_dir')) && is_dir($override_dir))
                        ? $override_dir
                        : $default_dir
                )
                ->set('field', $this);
        }

        if (func_num_args() === 0) {
            return $this->viewer;
        }

        return $this->viewer->make("_override::{$view}", $data);
    }

    /**
     * @inheritDoc
     */
    public function setPlugin(TinyMce $plugin): TinyMceFieldContract
    {
        $this->plugin = $plugin;

        return $this;
    }
}