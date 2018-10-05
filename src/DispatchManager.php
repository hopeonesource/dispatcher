<?php

namespace Drupal\dispatcher;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * Class HosMessageManager.
 */
class DispatchManager implements DispatchManagerInterface {

  protected $httpClient;
  protected $loggerChannelFactory;

  /**
   * HosMessageManager constructor.
   *
   * @param \GuzzleHttp\Client $httpClient
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   */
  public function __construct(Client $httpClient, LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->httpClient = $httpClient;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * @inheritdoc
   */
  public function sendMessage($message, $number){

    $this->loggerChannelFactory->get('dispatcher')->notice($message.' sent to '.$number);
      }
}
