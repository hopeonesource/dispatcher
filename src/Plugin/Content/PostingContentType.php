<?php

namespace Drupal\dispatcher\Plugin\Content;

use Drupal\Core\Entity\EntityFieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define a concrete class for a Dispatcher
 *
 * @ContentType(
 *     id = "community",
 *     name = @Translation("Posting")
 * )
 */
class PostingContentType extends AbstractContent {
    protected $entityFieldManager;

    public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityFieldManager $entityFieldManager) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->entityFieldManager = $entityFieldManager;
    }
    public function getContentTypeFields(){
        $types = ['string', 'text_with_summary'];
        $fieldDefinitions = $this->entityFieldManager->getFieldDefinitions('node', $this->getPluginId());
        foreach ($fieldDefinitions as $field_name => $field_definition) {
            if (!empty($field_definition->getTargetBundle())) {
                if (in_array($field_definition->getType(), $types)){
                    $listFields[$field_name]['type'] = $field_definition->getType();
                }
            }
        }
        $fields = [];

        foreach (array_keys($listFields) as $name) {
            $fields[$name] = $name;
        }
        return $fields;
    }
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static($configuration, $plugin_id, $plugin_definition,
            $container->get('entity_field.manager')
        );
    }
}