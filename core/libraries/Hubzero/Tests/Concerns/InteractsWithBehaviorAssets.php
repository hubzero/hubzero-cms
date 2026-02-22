<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests\Concerns;

/**
 * Shared assertions for behavior-registered document assets.
 */
trait InteractsWithBehaviorAssets
{
    /**
     * @param   object  $document
     * @param   string  $fragment
     * @return  void
     */
    protected function assertScriptRegistered(object $document, string $fragment): void
    {
        $scripts = isset($document->scripts) && is_array($document->scripts)
            ? implode("\n", $document->scripts)
            : '';

        $this->assertStringContainsString($fragment, $scripts);
    }

    /**
     * @param   object  $document
     * @param   string  $fragment
     * @return  void
     */
    protected function assertStylesheetRegistered(object $document, string $fragment): void
    {
        $stylesheets = '';
        if (isset($document->stylesheets) && is_array($document->stylesheets)) {
            $stylesheets = implode("\n", array_map(static function ($style): string {
                if (is_array($style) && isset($style['url'])) {
                    return (string) $style['url'];
                }
                return '';
            }, $document->stylesheets));
        }

        $this->assertStringContainsString($fragment, $stylesheets);
    }
}
