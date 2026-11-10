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

class TraitDefaultHtmxComponent extends AbstractComponent implements HtmxComponentInterface
{
    use UsesHtmxComponent;

    protected function execute(): void
    {
    }
}
