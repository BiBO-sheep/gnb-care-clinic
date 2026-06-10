<?php

namespace App\Logging;

use Monolog\Logger;

class TelegramLoggerFactory
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config): Logger
    {
        $logger = new Logger('telegram');
        $logger->pushHandler(new TelegramLogHandler(
            level: $config['level'] ?? 'error'
        ));
        
        return $logger;
    }
}
