<?php

namespace Drupal\hos_sms_dispatcher\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigManagerInterface;

/**
 * Class HosMessageManagerForm.
 */
class HosMessageManagerForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'hos_sms_dispatcher.hosmessagemanager',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'hos_sms_dispatcher_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('hos_sms_dispatcher.hosmessagemanager');
    $sendStatus = $config->get('sms_send_status');

    $form['hos_sms_dispatcher'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('General Messaging Settings'),
    );
    $form['hos_sms_dispatcher']['tropo_base_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Tropo base url'),
      '#default_value' => $config->get('tropo_base_url'),
      '#required' => TRUE,
    );
    $form['hos_sms_dispatcher']['access_token'] = array(
      '#type' => 'textfield',
      '#title' => t('Access Token'),
      '#default_value' => $config->get('access_token'),
      '#description' => t("Please enter your Tropo Access token"),
      '#required' => TRUE,
    );
    $form['hos_sms_dispatcher']['sms_count'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of SMS Messages to Send Per Minutes'),
      '#default_value' => $config->get('sms_count'),
      '#description' => t("Please enter the number of texts to send per minute"),
      '#required' => TRUE,
    );
    $form['hos_sms_dispatcher']['sms_time_between_texts'] = array(
      '#type' => 'textfield',
      '#title' => t('Number of Between Each Message in Cron Job'),
      '#default_value' => $config->get('sms_time_between_texts'),
      '#description' => t("Please enter the number of seconds between each text message in a batch"),
      '#required' => TRUE,
    );
    $form['hos_sms_dispatcher']['sms_testing'] = array(
      '#type' => 'checkbox',
      '#title' => t('Turn off texting - WARNING: No texts will be sent. This is for when you are testing.'),
      '#default_value' => $config->get('sms_testing'),
      '#description' => t("Do you want to turn off SMS texts from being sent?"),
    );
    $form['hos_sms_dispatcher']['sms_logging'] = array(
      '#type' => 'checkbox',
      '#title' => t('Turn on logging for Drupal side of messaging'),
      '#default_value' => $config->get('sms_logging'),
      '#description' => t("Do you want to turn on logging for the system?"),
    );
    $form['hos_sms_dispatcher']['default_message'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Default message'),
      '#description' => t('Default message sent out to new subscribers.'),
      '#default_value' => $config->get('default_message'),
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

    $this->config('hos_sms_dispatcher.hosmessagemanager')
      ->set('tropo_base_url', $form_state->getValue('tropo_base_url'))
      ->set('access_token', $form_state->getValue('access_token'))
      ->set('sms_testing', $form_state->getValue('sms_testing'))
      ->set('sms_count', $form_state->getValue('sms_count'))
      ->set('sms_time_between_texts', $form_state->getValue('sms_time_between_texts'))
      ->set('sms_testing', $form_state->getValue('sms_testing'))
      ->set('sms_logging', $form_state->getValue('sms_logging'))
      ->set('default_message', $form_state->getValue('default_message'))
      ->save();
  }
}
