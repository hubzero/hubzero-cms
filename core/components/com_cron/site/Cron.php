<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cron\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Cron extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        // Instantiate controller
        $controller = new Controllers\Jobs();
        $controller->execute();
    }
}
