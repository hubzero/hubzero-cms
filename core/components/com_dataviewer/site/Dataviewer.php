<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Site;

use Hubzero\Component\AbstractComponent;

class Dataviewer extends AbstractComponent
{
    protected function execute(): void
    {
        DvConfig::init();
        Controller::dispatch();
    }
}
