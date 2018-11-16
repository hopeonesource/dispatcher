<?php

namespace Drupal\dispatcher;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\sms\Provider\SmsProviderInterface;
use Drupal\sms\Entity\SmsMessage;
use Drupal\sms\Entity\SmsGateway;
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

    public function __construct(SmsProviderInterface $smsProvider) {
        $this->smsProvider = $smsProvider;
        $this->httpClient = \Drupal::httpClient();
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
        $results = $this->smsProvider->send($message);
        $this->assessResults($results);
    }

    /**
     * Sending a message successfully does not return DELIVERED,
     * that status can only be gathered through Status Callback
     * https://www.twilio.com/docs/sms/tutorials/how-to-confirm-delivery
     *
     * @var SmsMessageResult[]
     */
    protected function assessResults($results) {
        foreach ($results as $result) {
            foreach ($result->getReports() as $report) {
                if (strcmp($report->getStatus(), SmsMessageReportStatus::QUEUED) == 0) {
                    $this->alertSlack('*Success* from the Twilio API!');
                } else {
                    $this->alertSlack('*Failure* from the Twilio API!');
                }
            }
        }
    }

    /**
     * Send a message to a slack channel.
     *
     * Uses Slack App to send message. Follow instructions here:
     * https://api.slack.com/slack-apps
     *
     * Slack-apps can be integrated with messaging to individual
     * users for testing.
     */
    protected function alertSlack($message) {
        $this->httpClient->post('https://hooks.slack.com/services/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', [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'json' => [
                'text' => $message
            ]
        ]);
    }
}
