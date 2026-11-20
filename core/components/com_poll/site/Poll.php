<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Poll extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        // Require the base controller

        $controller = new Controllers\Polls();
        $controller->execute();
    }
}
