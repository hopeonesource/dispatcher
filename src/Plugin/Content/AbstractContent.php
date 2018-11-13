<?php
namespace Drupal\dispatcher\Plugin\Content;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractContent extends PluginBase implements ContentTypeInterface, ContainerFactoryPluginInterface {
    use StringTranslationTrait;

    public function __construct(array $configuration, $plugin_id, $plugin_definition){
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition){
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition
        );
    }
    public function getContentTypeName() {
        return $this->pluginDefinition['name'];
    }
}