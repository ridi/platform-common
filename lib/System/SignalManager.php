<?php

namespace Ridibooks\Platform\Common\System;

class SignalManager
{
    /** @var string pcntl_signal의 handler */
    public const PCNTL_SIGNAL_HANDLER = 'Ridibooks\Platform\Common\System\SignalManager::signalHandler';

    /** @var bool */
    private static $is_continue = true;

    public static function signalHandler(int $signo): void
    {
        if ($signo === SIGTERM) {
            self::$is_continue = false;
        }
    }

    public static function isContinued(): bool
    {
        return self::$is_continue;
    }
}
