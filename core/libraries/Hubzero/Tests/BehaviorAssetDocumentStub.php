<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Tests;

class BehaviorAssetDocumentStub
{
    /**
     * @var  array<int, string>
     */
    public array $scripts = [];
    /**
     * @var  array<int, array<string, mixed>>
     */
    public array $stylesheets = [];

    public function addScript(
        string $url,
        string $type = 'text/javascript',
        bool $defer = false,
        bool $async = false,
    ): void {
        $this->scripts[] = $url;
    }

    /**
     * @param   array<string, mixed>  $attribs
     */
    public function addStylesheet(
        string $url,
        string $type = 'text/css',
        ?string $media = null,
        array $attribs = [],
    ): void {
        $this->stylesheets[] = [
            'url' => $url,
            'attribs' => $attribs,
        ];
    }

    public function instance(): object
    {
        return new \stdClass();
    }
}
