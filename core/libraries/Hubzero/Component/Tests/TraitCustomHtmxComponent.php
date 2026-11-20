<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\AbstractComponent;
use Hubzero\Component\HtmxComponentInterface;
use Hubzero\Component\Traits\UsesHtmxComponent;

class TraitCustomHtmxComponent extends AbstractComponent implements HtmxComponentInterface
{
    use UsesHtmxComponent;

    /**
     * @return  array<string, mixed>
     */
    protected function htmxSecurityConfig(): array
    {
        return [
            'allowEval' => true,
            'allowScriptTags' => true,
            'historyCacheSize' => 5,
        ];
    }

    protected function execute(): void {}
}
