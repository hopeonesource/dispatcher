<?php

namespace Drupal\dispatcher;

/**
 * Interface HosMessageManagerInterface.
 */
interface DispatchManagerInterface {

  /**
   * Attempts to send message directly, without queueing
   * 
   * @param $message
   *
   * message to be sent
   *
   * @param $recipientNumber
   *
   * phone number that should receive this message
   *
   * @return string
   *
   * http status code
   */
  public function sendMessage($message, $recipientNumber);

  /**
   * Queues message to be sent as recommended by SmsMessage
   * 
   * @param $message
   *
   * message to be sent
   *
   * @param $recipientNumber
   *
   * phone number that should receive this message
   *
   */
  public function queueMessage($messageText, $recipientNumber);
}
