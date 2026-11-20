<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Mailto\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Mailto extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {

        $controller = new Controllers\Mailings();
        $controller->execute();
    }
}
