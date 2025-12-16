<?php

namespace App\Services;

use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;
use Illuminate\Support\Facades\Log;

class VonageService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $apiKey = env('VONAGE_API_KEY');
        $apiSecret = env('VONAGE_API_SECRET');
        $this->fromNumber = env('VONAGE_FROM_NUMBER', 'VIJO');

        if (empty($apiKey) || empty($apiSecret)) {
            throw new \Exception('Vonage API credentials are not configured.');
        }

        $basic = new Basic($apiKey, $apiSecret);
        $this->client = new Client($basic);
    }

    /**
     * Send SMS using Vonage
     *
     * @param string $to Phone number in E.164 format (e.g., +15551234567)
     * @param string $message Message text to send
     * @return array Response with status and message details
     */
    public function sendSms($to, $message)
    {
        try {
            // Ensure phone number is in E.164 format
            if (!str_starts_with($to, '+')) {
                $to = '+' . $to;
            }

            $sms = new SMS($to, $this->fromNumber, $message);
            $response = $this->client->sms()->send($sms);

            $messageResponse = $response->current();

            if ($messageResponse->getStatus() == 0) {
                Log::info('Vonage SMS sent successfully', [
                    'to' => $to,
                    'message_id' => $messageResponse->getMessageId(),
                    'network' => $messageResponse->getNetwork(),
                ]);

                return [
                    'success' => true,
                    'message_id' => $messageResponse->getMessageId(),
                    'status' => $messageResponse->getStatus(),
                    'network' => $messageResponse->getNetwork(),
                ];
            } else {
                $errorMessage = $messageResponse->getStatus() . ': ' . 
                               ($messageResponse->getErrorText() ?? 'Unknown error');

                Log::error('Vonage SMS failed', [
                    'to' => $to,
                    'status' => $messageResponse->getStatus(),
                    'error' => $messageResponse->getErrorText(),
                ]);

                return [
                    'success' => false,
                    'status' => $messageResponse->getStatus(),
                    'error' => $messageResponse->getErrorText(),
                ];
            }
        } catch (\Exception $e) {
            Log::error('Vonage SMS exception', [
                'to' => $to,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get account balance
     *
     * @return float|null
     */
    public function getBalance()
    {
        try {
            $balance = $this->client->account()->getBalance();
            return floatval($balance->getBalance());
        } catch (\Exception $e) {
            Log::error('Error getting Vonage balance: ' . $e->getMessage());
            return null;
        }
    }
}
