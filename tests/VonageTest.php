<?php

/**
 * Vonage SMS Test Script
 * 
 * Este script testa a integração com Vonage SMS API
 * Execute via: php artisan tinker
 * E depois: include 'tests/VonageTest.php';
 */

namespace Tests;

use App\Services\VonageService;
use Illuminate\Support\Facades\Log;

class VonageTest
{
    /**
     * Test 1: Check if Vonage is properly configured
     */
    public static function testConfiguration()
    {
        echo "\n=== Test 1: Configuration Check ===\n";
        
        $apiKey = env('VONAGE_API_KEY');
        $apiSecret = env('VONAGE_API_SECRET');
        $fromNumber = env('VONAGE_FROM_NUMBER');
        
        echo "API Key: " . ($apiKey ? "✓ Configured" : "✗ Missing") . "\n";
        echo "API Secret: " . ($apiSecret ? "✓ Configured" : "✗ Missing") . "\n";
        echo "From Number: " . ($fromNumber ? $fromNumber : "✗ Missing") . "\n";
        
        if (!$apiKey || !$apiSecret) {
            echo "\n❌ Configuration incomplete! Please set VONAGE_API_KEY and VONAGE_API_SECRET in .env\n";
            return false;
        }
        
        echo "\n✅ Configuration looks good!\n";
        return true;
    }
    
    /**
     * Test 2: Check account balance
     */
    public static function testBalance()
    {
        echo "\n=== Test 2: Account Balance ===\n";
        
        try {
            $vonage = new VonageService();
            $balance = $vonage->getBalance();
            
            if ($balance !== null) {
                echo "✅ Account Balance: €" . number_format($balance, 2) . "\n";
                
                if ($balance < 1.00) {
                    echo "⚠️  Warning: Low balance! Please recharge your account.\n";
                }
                
                return true;
            } else {
                echo "❌ Could not retrieve balance. Check credentials.\n";
                return false;
            }
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test 3: Send test SMS
     */
    public static function testSendSms($to = '+18015988999', $message = null)
    {
        echo "\n=== Test 3: Send Test SMS ===\n";
        echo "To: {$to}\n";
        
        if (!$message) {
            $message = "Test message from VIJO via Vonage API at " . date('H:i:s');
        }
        
        echo "Message: {$message}\n\n";
        
        try {
            $vonage = new VonageService();
            $result = $vonage->sendSms($to, $message);
            
            if ($result['success']) {
                echo "✅ SMS Sent Successfully!\n";
                echo "Message ID: " . $result['message_id'] . "\n";
                echo "Status: " . $result['status'] . "\n";
                echo "Network: " . ($result['network'] ?? 'N/A') . "\n";
                return true;
            } else {
                echo "❌ SMS Failed!\n";
                echo "Status: " . ($result['status'] ?? 'N/A') . "\n";
                echo "Error: " . ($result['error'] ?? 'Unknown') . "\n";
                return false;
            }
        } catch (\Exception $e) {
            echo "❌ Exception: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test 4: Send verification code (like real usage)
     */
    public static function testVerificationCode($to = '+18015988999')
    {
        echo "\n=== Test 4: Send Verification Code ===\n";
        echo "To: {$to}\n";
        
        $code = rand(100000, 999999);
        $message = "Vijo verification code: {$code}\n\n";
        $message .= "Reply STOP to opt-out of SMS messages and receive codes via email only. ";
        $message .= "Reply HELP for support. Msg & data rates may apply.";
        
        echo "Code: {$code}\n\n";
        
        return self::testSendSms($to, $message);
    }
    
    /**
     * Run all tests
     */
    public static function runAll($testPhoneNumber = '+18015988999')
    {
        echo "\n";
        echo "╔════════════════════════════════════════╗\n";
        echo "║   VONAGE SMS INTEGRATION TEST SUITE    ║\n";
        echo "╚════════════════════════════════════════╝\n";
        
        $results = [];
        
        // Test 1: Configuration
        $results['config'] = self::testConfiguration();
        
        if (!$results['config']) {
            echo "\n❌ Tests aborted due to configuration issues.\n";
            return;
        }
        
        // Test 2: Balance
        $results['balance'] = self::testBalance();
        
        // Test 3: Simple SMS
        echo "\n⚠️  This will send a REAL SMS and consume credits!\n";
        echo "Continue? (yes/no): ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $response = trim(strtolower($line));
        fclose($handle);
        
        if ($response === 'yes' || $response === 'y') {
            $results['send_sms'] = self::testSendSms($testPhoneNumber);
            
            // Test 4: Verification Code
            echo "\nSend verification code test? (yes/no): ";
            $handle = fopen("php://stdin", "r");
            $line = fgets($handle);
            $response = trim(strtolower($line));
            fclose($handle);
            
            if ($response === 'yes' || $response === 'y') {
                $results['verification'] = self::testVerificationCode($testPhoneNumber);
            }
        } else {
            echo "\n⏭️  SMS tests skipped.\n";
        }
        
        // Summary
        echo "\n";
        echo "╔════════════════════════════════════════╗\n";
        echo "║          TEST RESULTS SUMMARY          ║\n";
        echo "╚════════════════════════════════════════╝\n";
        
        foreach ($results as $test => $passed) {
            $status = $passed ? "✅ PASS" : "❌ FAIL";
            $testName = str_pad(ucwords(str_replace('_', ' ', $test)), 20);
            echo "{$testName}: {$status}\n";
        }
        
        $total = count($results);
        $passed = count(array_filter($results));
        
        echo "\nTotal: {$passed}/{$total} tests passed\n";
        
        if ($passed === $total) {
            echo "\n🎉 All tests passed! Vonage integration is working correctly.\n";
        } else {
            echo "\n⚠️  Some tests failed. Please check the logs above.\n";
        }
    }
}

// Quick usage examples
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║               VONAGE TEST SCRIPT LOADED                    ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\nAvailable commands:\n";
echo "  Tests\\VonageTest::testConfiguration()          - Check config\n";
echo "  Tests\\VonageTest::testBalance()                - Check balance\n";
echo "  Tests\\VonageTest::testSendSms('+1234567890')  - Send test SMS\n";
echo "  Tests\\VonageTest::testVerificationCode()      - Send verification\n";
echo "  Tests\\VonageTest::runAll('+1234567890')       - Run all tests\n";
echo "\nExample:\n";
echo "  Tests\\VonageTest::runAll('+18015988999');\n";
echo "\n";
