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
    /** @var array<int, array{message: mixed, context: array<mixed>}> */
    public array $notices = [];

    /** @var array<int, array{level: mixed, message: mixed, context: array<mixed>}> */
    public array $allLogs = [];

    /**
     * @param   mixed  $message
     */
    public function log(
        $level,
        $message,
        array $context = [],
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
