<?php declare(strict_types=1);

namespace tiFy\Plugins\TinyMce\Contracts;

use tiFy\Contracts\Field\FieldFactory as FieldFactoryContract;

interface TinyMceField extends FieldFactoryContract
{
    /**
     * Définition de l'instance du gestionnaire de plugin.
     *
     * @param TinyMce $plugin
     *
     * @return static
     */
    public function setPlugin(TinyMce $tinymce): TinyMceField;
}