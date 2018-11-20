<?php

namespace Drupal\dispatcher;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\sms\Provider\SmsProviderInterface;
use Drupal\sms\Entity\SmsMessage;
use Drupal\sms\Entity\SmsGateway;

/**
 * Class DispatchManager.
 */
class DispatchManager implements DispatchManagerInterface {
    /**
     * @var SmsProviderInterface
     */
    protected $smsProvider;

    public function __construct(SmsProviderInterface $smsProvider) {
        $this->smsProvider = $smsProvider;
    }

    /**
     * @inheritdoc
     */
    public function sendMessage($messageText, $number) {
        $date = new DrupalDateTime();
        $message = SmsMessage::create()
            ->addRecipient($number)
            ->setMessage($messageText)
            ->setAutomated(1);
        $message->setSendTime($date->format('U'));
        $message->setGateway(SmsGateway::load('twilio'));
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
}
