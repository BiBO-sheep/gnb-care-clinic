<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Illuminate\Support\Facades\Http;

class TelegramLogHandler extends AbstractProcessingHandler
{
    public function __construct(string|Level $level = Level::Error)
    {
        $level = is_string($level) ? Level::fromName($level) : $level;
        parent::__construct($level);
    }

    protected function write(LogRecord $record): void
    {
        $botToken  = config('services.telegram.bot_token');
        $chatId    = config('services.telegram.chat_id');

        if (!$botToken || !$chatId) {
            return;
        }

        $emoji = match ($record->level) {
            Level::Emergency, Level::Alert, Level::Critical => '🔴',
            Level::Error    => '🟠',
            Level::Warning  => '🟡',
            default         => 'ℹ️',
        };

        $env = config('app.env');
        $appName = config('app.name');
        
        // Escape special markdown characters for Telegram MarkdownV2 or use HTML
        // Using basic Markdown here. Be careful with characters.
        $messageText = mb_substr($record->message, 0, 3500);

        $text = "{$emoji} *" . strtoupper($record->level->name) . "* — {$appName}\n\n"
            . "```\n{$messageText}\n```\n\n"
            . "📅 " . now()->format('Y-m-d H:i:s') . "\n"
            . "🌐 {$env}";

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'    => $chatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
            ]);
        } catch (\Throwable $e) {
            // Silently fail to avoid infinite error loops
        }
    }
}
