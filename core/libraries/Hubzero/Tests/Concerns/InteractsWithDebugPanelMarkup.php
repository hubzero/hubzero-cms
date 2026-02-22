<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests\Concerns;

/**
 * Shared assertion helpers for protocol debug panel markup.
 */
trait InteractsWithDebugPanelMarkup
{
    /**
     * @param   string  $html
     * @param   string  $protocol
     * @param   string  $panelClass
     * @param   string  $title
     * @return  void
     */
    protected function assertDebugPanelMarkup(string $html, string $protocol, string $panelClass, string $title): void
    {
        $this->assertStringContainsString($panelClass, $html);
        $this->assertStringContainsString($title, $html);
        $this->assertStringContainsString("__hzDebugPanel('" . strtolower($protocol) . "'", $html);
        $this->assertStringContainsString('x-model="timelineKind"', $html);
        $this->assertStringContainsString('panelData()', $html);
    }
}
