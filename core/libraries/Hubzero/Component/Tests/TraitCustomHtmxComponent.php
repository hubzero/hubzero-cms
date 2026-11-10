<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Tests;

use Hubzero\Component\AbstractComponent;
use Hubzero\Component\HtmxComponentInterface;
use Hubzero\Component\Traits\UsesHtmxComponent;

class TraitCustomHtmxComponent extends AbstractComponent implements HtmxComponentInterface
{
    use UsesHtmxComponent;

    protected function htmxSecurityConfig(): array
    {
        return [
            'allowEval' => true,
            'allowScriptTags' => true,
            'historyCacheSize' => 5,
        ];
    }

    protected function execute(): void
    {
    }
}
