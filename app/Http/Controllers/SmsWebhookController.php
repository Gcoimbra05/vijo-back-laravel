<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class SmsWebhookController extends Controller
{
    /**
     * Handle incoming SMS responses (STOP, START, HELP)
     */
    public function handleIncoming(Request $request)
    {
        $from = $request->input('From'); // Phone number with country code
        $body = trim(strtoupper($request->input('Body', '')));
        
        Log::info('Incoming SMS webhook', ['from' => $from, 'body' => $body]);

        // Clean phone number (remove +)
        $cleanPhone = preg_replace('/[^0-9]/', '', $from);
        
        // Find user by phone number
        $user = User::where('mobile', 'LIKE', '%' . substr($cleanPhone, -10))->first();
        
        $responseMessage = '';
        
        switch ($body) {
            case 'STOP':
            case 'STOPALL':
            case 'UNSUBSCRIBE':
            case 'CANCEL':
            case 'END':
            case 'QUIT':
                // Opt-out: Disable SMS for this user
                if ($user) {
                    $user->sms_opt_out = true;
                    $user->save();
                }
                $responseMessage = "Vijo: You've successfully unsubscribed and will receive no further one-time passcode messages using your mobile device. Type START to opt-in.";
                break;
                
            case 'START':
            case 'UNSTOP':
                // Opt-in: Enable SMS for this user
                if ($user) {
                    $user->sms_opt_out = false;
                    $user->save();
                }
                $responseMessage = "Vijo: You've successfully subscribed to receive one-time passcode messages for authentication purposes. Reply STOP to cancel and receive one-time passcode messages via email only. Reply HELP for help. Message and Data rates may apply. Message frequency varies.";
                break;
                
            case 'HELP':
            case 'INFO':
                $responseMessage = "Vijo: For HELP, please email at support@vijo.com";
                break;
                
            default:
                // Unknown command, do nothing or log
                Log::info('Unknown SMS command received', ['body' => $body]);
                break;
        }
        
        // Return TwiML response
        header('Content-Type: text/xml');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<Response>';
        if ($responseMessage) {
            echo '<Message>' . htmlspecialchars($responseMessage) . '</Message>';
        }
        echo '</Response>';
        exit;
    }
}
