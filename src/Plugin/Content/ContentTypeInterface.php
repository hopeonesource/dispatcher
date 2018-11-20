<?php

namespace Drupal\dispatcher\Plugin\Content;

/**
 * Interface ContentTypeInterface
 *
 * Defines an interface for dispatcher plugins.
 *
 * @package Drupal\dispatcher\Plugin\Content
 */
interface ContentTypeInterface {
    /**
     *
     *
     * @return array of available content type fields
     */
    public function getContentTypeFields();
    /**
     * Return the machine name of the content type associated with this plugin.
     *
     * @return string
     *   returns the name as a string.
     */
    public function getContentTypeName();
}