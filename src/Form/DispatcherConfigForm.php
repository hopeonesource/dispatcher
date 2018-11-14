<?php
namespace Drupal\dispatcher\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class DispatcherConfigForm extends ConfigFormBase {
    private $plugin_manager;

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
        $this->plugin_manager = \Drupal::service('plugin.manager.dispatcher');
        $plugin_definitions = $this->plugin_manager->getDefinitions();

        $contentType = $config->get('content_type');
        $messageField = $config->get('message_field');

        if (isset($contentType)){
            $instance = $this->plugin_manager->createInstance($contentType);
        }
        else{
            $instance = $this->plugin_manager->createInstance(array_values($plugin_definitions)[0]['id']);
        }

        $fields = [];
        $content_type_options = array();
        foreach ($plugin_definitions as $plugin_definition){
            $content_type_options[$plugin_definition['id']] = $plugin_definition['name'];
            $fields = $instance->getContentTypeFields();
        }

        $form['#attached']['library'][] = 'dispatcher/dispatcher_settings';
        $form['content_type'] = array (
            '#title' => t('Content type'),
            '#type' => 'select',
            '#options' => $content_type_options,
            '#default_value' => isset($contentType) ? $contentType : '',
            '#ajax' => array(
                'callback' => '::getFields',
                'event' => 'change',
            ),
            '#required' => TRUE,
        );
        $form['message_field'] = array(
            '#title' => t('Message field'),
            '#type' => 'radios',
            '#options' => $fields,
            '#default_value' => isset($messageField) ? $messageField : '',
            '#required' => TRUE,
            '#prefix' => '<div id="message-fields">',
            '#suffix' => '</div>',
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
            ->save();
    }
    public function getFields(array &$form, FormStateInterface $form_state){
        $config = $this->config('dispatcher.config');
        $contentType = $config->get('content_type');
        $msgField = $config->get('message_field');

        $selectedContentType = $form_state->getValue('content_type');

        $ajax_response = new AjaxResponse();
        $instance = $this->plugin_manager->createInstance($selectedContentType);
        $fields = $instance->getContentTypeFields();

        $msg = '';
        foreach ($fields as $field){
            if ($field == $msgField && $contentType == $selectedContentType){
                $msg .= "<div class=\"js-form-item form-item js-form-type-radio form-type-radio js-form-item-message-field form-item-message-field\">";
                $msg .= "<input checked=\"checked\" type=radio name=message_field class=\"form-radio\" required=required  value=".$field."><label for=\"edit-message-field-title\" class=\"option\" >".$field."</label>";
                $msg .= "</div>";
            }
            else{
                $msg .= "<div class=\"js-form-item form-item js-form-type-radio form-type-radio js-form-item-message-field form-item-message-field\">";
                $msg .= "<input type=radio name=message_field class=\"form-radio\" required=required value=".$field."><label for=\"edit-message-field-title\" class=option>".$field."</label>";
                $msg .= "</div>";
            }
        }

        $ajax_response->addCommand(new HtmlCommand('.fieldset-wrapper', $msg));

        return $ajax_response;
    }
}