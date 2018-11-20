<?php
namespace Drupal\dispatcher\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a ContentType annotation object.
 *
 *
 * @Annotation
 */
class ContentType extends Plugin {
    /**
     * The plugin ID.
     *
     * @var string
     */
    public $id;
    /**
     * The plugin name.
     *
     * @var string
     */
    public $name;
}