<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Whatsnew\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Whatsnew extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        // Instantiate controller
        $controller = new Controllers\Results();
        $controller->execute();
    }
}
