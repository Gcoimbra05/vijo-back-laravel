<?php

namespace App\Console\Commands;

use App\Services\VonageService;
use Illuminate\Console\Command;

class TestVonageSms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vonage:test 
                            {action=all : Action to perform (config, balance, send, all)}
                            {--to= : Phone number to send test SMS (E.164 format)}
                            {--message= : Custom message to send}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Vonage SMS integration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║    VONAGE SMS INTEGRATION TEST         ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        switch ($action) {
            case 'config':
                return $this->testConfiguration();
            case 'balance':
                return $this->testBalance();
            case 'send':
                return $this->testSendSms();
            case 'all':
                return $this->runAllTests();
            default:
                $this->error("Invalid action: {$action}");
                $this->info("Available actions: config, balance, send, all");
                return 1;
        }
    }

    /**
     * Test configuration
     */
    protected function testConfiguration()
    {
        $this->info('=== Configuration Check ===');
        $this->newLine();

        $apiKey = env('VONAGE_API_KEY');
        $apiSecret = env('VONAGE_API_SECRET');
        $fromNumber = env('VONAGE_FROM_NUMBER');

        $this->line('API Key: ' . ($apiKey ? '<fg=green>✓ Configured</>' : '<fg=red>✗ Missing</>'));
        $this->line('API Secret: ' . ($apiSecret ? '<fg=green>✓ Configured</>' : '<fg=red>✗ Missing</>'));
        $this->line('From Number: ' . ($fromNumber ?: '<fg=red>✗ Missing</>'));

        if (!$apiKey || !$apiSecret) {
            $this->newLine();
            $this->error('Configuration incomplete!');
            $this->info('Please set VONAGE_API_KEY and VONAGE_API_SECRET in your .env file');
            return 1;
        }

        $this->newLine();
        $this->info('✅ Configuration looks good!');
        return 0;
    }

    /**
     * Test account balance
     */
    protected function testBalance()
    {
        $this->info('=== Account Balance ===');
        $this->newLine();

        try {
            $vonage = new VonageService();
            $balance = $vonage->getBalance();

            if ($balance !== null) {
                $this->info('✅ Account Balance: €' . number_format($balance, 2));
                
                if ($balance < 1.00) {
                    $this->warn('⚠️  Low balance! Please recharge your account.');
                }
                
                return 0;
            } else {
                $this->error('❌ Could not retrieve balance. Check credentials.');
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Test sending SMS
     */
    protected function testSendSms()
    {
        $to = $this->option('to');
        
        if (!$to) {
            $to = $this->ask('Enter phone number (E.164 format, e.g., +18015988999)');
        }

        if (!$to) {
            $this->error('Phone number is required!');
            return 1;
        }

        $message = $this->option('message');
        
        if (!$message) {
            $code = rand(100000, 999999);
            $message = "Vijo verification code: {$code}\n\n";
            $message .= "Reply STOP to opt-out of SMS messages and receive codes via email only. ";
            $message .= "Reply HELP for support. Msg & data rates may apply.";
        }

        $this->info('=== Send Test SMS ===');
        $this->line('To: ' . $to);
        $this->line('Message: ' . substr($message, 0, 50) . '...');
        $this->newLine();

        if (!$this->confirm('This will send a REAL SMS and consume credits. Continue?', false)) {
            $this->info('Test cancelled.');
            return 0;
        }

        try {
            $vonage = new VonageService();
            $result = $vonage->sendSms($to, $message);

            if ($result['success']) {
                $this->info('✅ SMS Sent Successfully!');
                $this->line('Message ID: ' . $result['message_id']);
                $this->line('Status: ' . $result['status']);
                $this->line('Network: ' . ($result['network'] ?? 'N/A'));
                return 0;
            } else {
                $this->error('❌ SMS Failed!');
                $this->line('Status: ' . ($result['status'] ?? 'N/A'));
                $this->line('Error: ' . ($result['error'] ?? 'Unknown'));
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Exception: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Run all tests
     */
    protected function runAllTests()
    {
        $results = [];

        // Test 1: Configuration
        $results['config'] = $this->testConfiguration() === 0;
        $this->newLine(2);

        if (!$results['config']) {
            $this->error('Tests aborted due to configuration issues.');
            return 1;
        }

        // Test 2: Balance
        $results['balance'] = $this->testBalance() === 0;
        $this->newLine(2);

        // Test 3: Send SMS (optional)
        if ($this->confirm('Run SMS sending test? (This will consume credits)', false)) {
            $results['send'] = $this->testSendSms() === 0;
            $this->newLine(2);
        }

        // Summary
        $this->info('╔════════════════════════════════════════╗');
        $this->info('║        TEST RESULTS SUMMARY            ║');
        $this->info('╚════════════════════════════════════════╝');
        $this->newLine();

        foreach ($results as $test => $passed) {
            $status = $passed ? '<fg=green>✅ PASS</>' : '<fg=red>❌ FAIL</>';
            $testName = str_pad(ucwords(str_replace('_', ' ', $test)), 20);
            $this->line("{$testName}: {$status}");
        }

        $total = count($results);
        $passed = count(array_filter($results));

        $this->newLine();
        $this->info("Total: {$passed}/{$total} tests passed");

        if ($passed === $total) {
            $this->newLine();
            $this->info('🎉 All tests passed! Vonage integration is working correctly.');
            return 0;
        } else {
            $this->newLine();
            $this->warn('⚠️  Some tests failed. Please check the output above.');
            return 1;
        }
    }
}
