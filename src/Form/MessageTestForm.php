<?php

namespace Drupal\dispatcher\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\dispatcher\DispatchManagerInterface;

/**
 * Class MessageTestForm.
 */
class MessageTestForm extends FormBase {

  /**
   * Drupal\dispatcher\HosMessageManagerInterface definition.
   *
   * @var \Drupal\dispatcher\DispatchManagerInterface
   */
  protected $hosSmsDispatchManager;
  /**
   * Constructs a new MessageTestForm object.
   */
  public function __construct(DispatchManagerInterface $hosSmsDispatchManager) {
        $this->hosSmsDispatchManager = $hosSmsDispatchManager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('hos_sms_dispatch.manager')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'message_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dispatcher.messagetest');
    $form['test_phone_numbers'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Phone Number'),
      '#description' => $this->t('Please enter a number to send the test to. Multiple numbers can be separated by a comma'),
      '#default_value' => '',
    ];
    $form['test_message'] = array(
      '#type' => 'textarea',
      '#title' => t('Test Message to send'),
      '#default_value' => 'This is a test of HOS - out going',
      '#description' => t("Please enter the message to send"),
      '#required' => TRUE,
    );

    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Send Message'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->hosSmsDispatchManager->sendMessage($form_state->getValue('test_message'),
      $form_state->getValue('test_phone_numbers'));
  }

}
