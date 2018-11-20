<?php

namespace Drupal\dispatcher;

use Drupal\Core\Datetime\DrupalDateTime;
use GuzzleHttp\Client;
use Drupal\sms\Provider\SmsProviderInterface;
use Drupal\sms\Direction;
use Drupal\sms\Entity\SmsMessage;
use Drupal\sms\Entity\SmsGateway;
use Drupal\sms\Exception\RecipientRouteException;
use Drupal\sms\Exception\SmsDirectionException;
use Drupal\sms\Message\SmsMessageReportStatus;

/**
 * Class DispatchManager.
 */
class DispatchManager implements DispatchManagerInterface {
    /**
     * @var SmsProviderInterface
     */
    protected $smsProvider;

    /**
     * @var \Drupal::httpClient
     */
    protected $httpClient;

    public function __construct(SmsProviderInterface $smsProvider, Client $httpClient) {
        $this->smsProvider = $smsProvider;
        $this->httpClient = $httpClient;
    }

    /**
     * @inheritdoc
     */
    public function sendMessage($messageText, $number) {
        $message = $this->prepareMessage($messageText, $number);
        $result = $this->smsProvider->send($message);
        $smsMessageResult = current($result)->getResult();
        $reports = $smsMessageResult->getReports();
        $smsDeliveryReport = current($reports);
        
        return array(
            'message' => $smsDeliveryReport->getStatusMessage(),
            'code' => $smsDeliveryReport->getStatus(),
            'To_number' => $number
        );
    }


    /**
     * @inheritdoc
     */
    public function queueMessage($messageText, $number) {
        $message = $this->prepareMessage($messageText, $number);
        try {
            $this->smsProvider->queue($message);
        } catch (RecipientRouteException $e) {
            \Drupal::logger('dispatch manager')->warning('RecipientRouteException thrown in DispatchManager->sendMessage');
        } catch (SmsDirectionException $e) {
            \Drupal::logger('dispatch manager')->warning('SmsDirection Exception thrown in DispatchManager->sendMessage');            
        }
    }

    /**
     * @param $message
     *
     * message to be sent
     *
     * @param $number
     *
     * phone number that should receive this message
     *
     * @return SmsMessage
     *
     * the prepared message with recipient and message text
     */
    private function prepareMessage($messageText, $number) {
        $date = new DrupalDateTime();
        $message = SmsMessage::create()
            ->addRecipient($number)
            ->setMessage($messageText)
            ->setAutomated(1)
            ->setDirection(DIRECTION::OUTGOING);
        $message->setSendTime($date->format('U'));
        $message->setGateway(SmsGateway::load('twilio'));
        return $message;
    }
}