<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Kb\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Kb extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        // Instantiate controller
        $controller = new Controllers\Articles();
        $controller->execute();
    }
}
