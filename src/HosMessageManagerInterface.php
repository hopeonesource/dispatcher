<?php

namespace Drupal\hos_sms_dispatcher;

/**
 * Interface HosMessageManagerInterface.
 */
interface HosMessageManagerInterface {

  /**
   * @param $message message to be sent
   *
   * @param $recipientNumber - phone numbers that should receive this message
   *
   * @return string
   *
   * http status code
   */
  public function sendMessage($message, $recipientNumber);
}
