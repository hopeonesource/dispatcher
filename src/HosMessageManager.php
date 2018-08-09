<?php

namespace Drupal\hos_sms_dispatcher;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

/**
 * Class HosMessageManager.
 */
class HosMessageManager implements HosMessageManagerInterface {

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
    $tropoToken = 'hos_token'; // to be got from config
    $url = 'https://api.tropo.com/1.0/sessions?'; // to be got from config
    $msg = urlencode('HOS: Welcome to HopeOneSource: Alerts from local providers. 
    Reply HELP or go to hopeonesource.org for HELP. Reply STOP to cancel. 
    Msg&Data rates may apply. Max 50/month'); // to be got from config

    $url = $url . 'action=create&token=' . $tropoToken . '&message=' . $msg . '&number=' . $number;
    //$sendResult = $this->httpClient->get($url);

    $this->loggerChannelFactory->get('hos_sms_dispatcher')->notice($message.' sent to '.$number);
    /*$this->loggerChannelFactory->get('hos_sms_dispatcher')->notice('SMS status code %code to %url',
      array('%code' => $sendResult->getStatusCode(),
        '%url' => $url ));


    return $sendResult->getStatusCode();*/
  }
}
