<?php declare(strict_types=1);

namespace tiFy\Plugins\TinyMce\Contracts;

use Psr\Container\ContainerInterface;
use tiFy\Contracts\Container\Container;

interface TinyMce
{
    /**
     * Récupération de l'instance du conteneur d'injection de dépendances.
     *
     * @return Container|null
     */
    public function getContainer(): ?ContainerInterface;

    /**
     * Récupération de la liste des boutons de plugins externes déclarés dans la configuration.
     *
     * @param string $buttons Liste des boutons définis dans la configuration.
     *
     * @return void
     */
    public function getExternalPluginsButtons($buttons = ''): void;

    /**
     * Récupération de l'url vers les assets d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginAssetsUrl($name): string;

    /**
     * Récupération de l'url vers le scripts d'un plugin.
     *
     * @param string $name Nom de qualification du plugin.
     *
     * @return string
     */
    public function getPluginUrl($name): string;

    /**
     * Récupération du chemin absolu vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesDir($path = ''): string;

    /**
     * Récupération de l'url absolue vers le répertoire des ressources.
     *
     * @param string $path Chemin relatif du sous-repertoire.
     *
     * @return string
     */
    public function resourcesUrl($path = ''): string;

    /**
     * Définition des attributs de configuration additionnels.
     *
     * @param array $attrs Liste des attributs de configuration additionnels.
     *
     * @return static
     */
    public function setAdditionnalConfig($attrs): TinyMce;

    /**
     * Déclaration d'un plugin.
     *
     * @param ExternalPluginInterface $externalPlugin Instance de la classe du plugin externe.
     *
     * @return static
     */
    public function setExternalPlugin(ExternalPluginInterface $externalPlugin): TinyMce;
}
