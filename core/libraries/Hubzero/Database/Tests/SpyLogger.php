<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests;

/**
 * PSR-3 logger that captures notice-level messages
 */
class SpyLogger extends \Psr\Log\AbstractLogger
{
    /** @var array */
    public array $notices = [];

    /** @var array */
    public array $allLogs = [];

    public function log(
        $level,
        $message,
        array $context = []
    ): void {
        $this->allLogs[] = [
            'level'   => $level,
            'message' => $message,
            'context' => $context,
        ];

        if ($level === 'notice') {
            $this->notices[] = [
                'message' => $message,
                'context' => $context,
            ];
        }
    }
}
