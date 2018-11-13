<?php
namespace Drupal\dispatcher\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DispatcherConfigForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {

        return array(
            'dispatcher.config',
        );
    }
    /**
     *{@inheritdoc}
     */
    public function getFormId() {

        return 'dispatcher_config_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('dispatcher.config');
        $plugin_manager = \Drupal::service('plugin.manager.dispatcher');
        $plugin_definitions = $plugin_manager->getDefinitions();

        $contentType = $config->get('content_type');
        $messageField = $config->get('message_field');

        if (isset($contentType)){
            $instance = $plugin_manager->createInstance($contentType);
        }
        else{
            $instance = $plugin_manager->createInstance(array_values($plugin_definitions)[0]['id']);
        }

        $fields = [];
        $content_type_options = array();
        foreach ($plugin_definitions as $plugin_definition){
            $content_type_options[$plugin_definition['id']] = $plugin_definition['name'];
            $fields = $instance->getContentTypeFields();
        }

        $form['content_type'] = array (
            '#title' => t('Content type'),
            '#type' => 'select',
            '#options' => $content_type_options,
            '#default_value' => isset($contentType) ? $contentType : '',
            '#required' => TRUE,
        );
        $form['message_field'] = array(
            '#title' => t('Message field'),
            '#type' => 'radios',
            '#options' => $fields,
            '#default_value' => isset($messageField) ? $messageField : '',
        );

        return parent::buildForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        parent::submitForm($form, $form_state);

        $this->config('dispatcher.config')
            ->set('content_type', $form_state->getValue('content_type'))
            ->set('message_field', $form_state->getValue('message_field'))
            //->set('508_message', $form_state->getValue('508_message'))
            ->save();
    }
}