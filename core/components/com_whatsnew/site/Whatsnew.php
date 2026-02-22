<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
        require_once __DIR__ . DS . 'controllers' . DS . 'results.php';

        // Instantiate controller
        $controller = new Controllers\Results();
        $controller->execute();
    }
}
