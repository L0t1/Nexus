<?php

namespace App\Console\Commands;

use App\Services\MessageProcessor;
use Illuminate\Console\Command;

class SimulateWebhook extends Command
{
    protected $signature = 'nexus:simulate
        {--platform=whatsapp : Platform (instagram, whatsapp, facebook)}
        {--from=+38344123456 : Sender phone or ID}
        {--message= : Message text}';

    protected $description = 'Simulate an incoming webhook message for local development';

    public function handle(MessageProcessor $processor): int
    {
        $platform = $this->option('platform');
        $from = $this->option('from');
        $message = $this->option('message')
            ?: $this->ask('Message text', 'Dua me porosi ni hoodie, Prishtina rr. UCK nr 5, +383 44 111 222');

        $this->info("Simulating {$platform} message from {$from}...");
        $this->line("Message: {$message}");
        $this->newLine();

        if ($platform === 'whatsapp') {
            $payload = [
                'messages' => [[
                    'from' => $from,
                    'id' => 'wamid_sim_' . uniqid(),
                    'timestamp' => time(),
                    'type' => 'text',
                    'text' => ['body' => $message],
                ]],
                'contacts' => [[
                    'wa_id' => $from,
                    'profile' => ['name' => 'Test Customer'],
                ]],
                'metadata' => [
                    'phone_number_id' => 'sim_phone_id',
                ],
            ];
            $processor->processWhatsAppMessage($payload);
        } else {
            $payload = [
                'object' => $platform === 'instagram' ? 'instagram' : 'page',
                'entry' => [[
                    'messaging' => [[
                        'sender' => ['id' => $from],
                        'recipient' => ['id' => 'sim_page_id'],
                        'timestamp' => time(),
                        'message' => [
                            'mid' => 'mid_sim_' . uniqid(),
                            'text' => $message,
                        ],
                    ]],
                ]],
            ];
            $processor->processWebhook($payload);
        }

        $this->info('✓ Message processed successfully!');
        $this->line('Check your Inbox at http://localhost:8000/inbox');

        return Command::SUCCESS;
    }
}
