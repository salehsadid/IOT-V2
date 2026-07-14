<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    public static function sendAlert(string $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        Log::info("Attempting to send Telegram alert: {$message}");

        if (empty($botToken) || empty($chatId)) {
            Log::warning('Telegram credentials not set. Token: ' . ($botToken ? 'Set' : 'Empty') . ', ChatID: ' . ($chatId ? 'Set' : 'Empty'));
            return;
        }

        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        try {
            $response = Http::post($url, [
                'chat_id' => $chatId,
                'text'    => "🚨 *Parkinson Monitor Alert*\n\n" . $message,
                'parse_mode' => 'Markdown'
            ]);
            
            if ($response->successful()) {
                Log::info("Telegram alert sent successfully.");
            } else {
                Log::error("Telegram API Failed. HTTP " . $response->status() . " Response: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Telegram API Exception: ' . $e->getMessage());
        }
    }
}
