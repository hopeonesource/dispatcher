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

        // replace with dependency injection
        $contentTypes = \Drupal::service('entity.manager')->getStorage('node_type')->loadMultiple();
        $contentTypesList = [];
        $content_type_options = array();

        foreach ($contentTypes as $contentType) {
            array_push($contentTypesList, $contentType->id());
        }

        //check if plugin id is part of the list of available content types
        foreach ($plugin_definitions as $key => $plugin_definition) {
            if (!in_array($key, $contentTypesList) ){
                unset($plugin_definitions[$key]);
            }
            else{
                $content_type_options[$plugin_definition['id']] = $plugin_definition['name'];
            }
        }

        $contentType = $config->get('content_type');
        $messageField = $config->get('message_field');
        $recipientField = $config->get('recipient_field');

        if (isset($contentType)){
            $instance = $this->plugin_manager->createInstance($contentType);
        }
        else{
            $instance = $this->plugin_manager->createInstance(array_values($plugin_definitions)[0]['id']);
        }


        $fields = $instance->getContentTypeFields(['string', 'text_with_summary']);

        $form['#attached']['library'][] = 'dispatcher/dispatcher_settings';
        $form['content_type'] = array (
            '#title' => t('Content type'),
            '#type' => 'select',
            '#options' => $content_type_options,
            '#default_value' => isset($contentType) ? $contentType : '',
            /*'#ajax' => array(
                'callback' => '::getFields',
                'event' => 'change',
            ),*/
            '#required' => TRUE,
        );
        $form['message_field'] = array(
            '#title' => t('Message field'),
            '#type' => 'radios',
            '#options' => $fields,
            '#default_value' => isset($messageField) ? $messageField : '',
            '#required' => TRUE,
            '#prefix' => '<div id="message-fields" class="message-fields">',
            '#suffix' => '</div>',
        );
        $fields = $instance->getContentTypeFields(['integer', 'entity_reference', 'string', 'text_with_summary']);
        $form['recipient_field'] = array(
            '#title' => t('Recipient fields'),
            '#type' => 'checkboxes',
            '#options' => $fields,
            '#default_value' => isset($recipientField) ? $recipientField : '',
            '#required' => TRUE,
            '#prefix' => '<div id="recipient-fields" class="recipient-fields">',
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
            ->set('recipient_field', $form_state->getValue('recipient_field'))
            ->save();
    }
    //@Todo to be removed as configuration will support a single plugin only
    public function getFields(array &$form, FormStateInterface $form_state){
        $config = $this->config('dispatcher.config');
        $contentType = $config->get('content_type');
        $msgField = $config->get('message_field');
        $recipField = $config->get('recipient_field');

        $selectedContentType = $form_state->getValue('content_type');

        $ajax_response = new AjaxResponse();
        $instance = $this->plugin_manager->createInstance($selectedContentType);
        $fields = $instance->getContentTypeFields(['string', 'text_with_summary']);

        $msg = '';
        //@todo to be taken out to a private function
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
        $ajax_response->addCommand(new HtmlCommand('.message-fields .fieldset-wrapper', $msg));

        $fields = $instance->getContentTypeFields(['integer', 'entity_reference', 'string', 'text_with_summary']);
        $msg = '';
        foreach ($fields as $field){
            if ($field == $recipField && $contentType == $selectedContentType){
                $msg .= "<div class=\"js-form-item form-item js-form-type-radio form-type-radio js-form-item-recipient-field form-item-recipient-field\">";
                $msg .= "<input checked=\"checked\" type=radio name=recipient_field class=\"form-radio\" required=required  value=".$field."><label for=\"edit-recipient-field-title\" class=\"option\" >".$field."</label>";
                $msg .= "</div>";
            }
            else{
                $msg .= "<div class=\"js-form-item form-item js-form-type-radio form-type-radio js-form-item-recipient-field form-item-recipient-field\">";
                $msg .= "<input type=radio name=recipient_field class=\"form-radio\" required=required value=".$field."><label for=\"edit-recipient-field-title\" class=option>".$field."</label>";
                $msg .= "</div>";
            }
        }

        $ajax_response->addCommand(new HtmlCommand('.recipient-fields .fieldset-wrapper', $msg));

        return $ajax_response;
    }
}