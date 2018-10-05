<?php

namespace Drupal\dispatcher;

/**
 * Interface HosMessageManagerInterface.
 */
interface DispatchManagerInterface {

  /**
   * @param $message
   *
   * message to be sent
   *
   * @param $recipientNumber
   *
   * phone numbers that should receive this message
   *
   * @return string
   *
   * http status code
   */
  public function sendMessage($message, $recipientNumber);
}
