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
        $date = new DrupalDateTime();
        $message = SmsMessage::create()
            ->addRecipient($number)
            ->setMessage($messageText)
            ->setAutomated(1)
            ->setDirection(DIRECTION::OUTGOING);
        $message->setSendTime($date->format('U'));
        $message->setGateway(SmsGateway::load('twilio'));
        try {
            $results = $this->smsProvider->queue($message);
            $this->assessResults($results);
        } catch (RecipientRouteException $e) {
            \Drupal::logger('dispatch manager')->warning('RecipientRouteException thrown in DispatchManager->sendMessage');
        } catch (SmsDirectionException $e) {
            \Drupal::logger('dispatch manager')->warning('SmsDirection Exception thrown in DispatchManager->sendMessage');            
        }
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
                $status = $report->getStatus();
                if (strcmp($status, SmsMessageReportStatus::QUEUED) == 0) {
                    $this->alertSlack('*Success* from the Twilio API!', $status);
                } else {
                    $this->alertSlack('*Failure* from the Twilio API!', $status);
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
    protected function alertSlack($message, $status) {
        $color = strcmp($status, SmsMessageReportStatus::QUEUED) == 0 
            ? '#00ff00' : '#ff0000';
        $this->httpClient->post('https://hooks.slack.com/services/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', [
            'headers' => [
                'Content-type' => 'application/json'
            ],
            'json' => [
                'text' => $message,
                'attachments' => array([
                    'text' => 'Message Status: *' . $status . '*',
                    'color'    => $color,
                ])
            ]
        ]);
    }
}
