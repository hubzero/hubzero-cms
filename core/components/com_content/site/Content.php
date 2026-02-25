<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Site;

use Hubzero\Component\AbstractComponent;

/**
 * Component entry point
 */
class Content extends AbstractComponent
{
    /**
     * Entry point
     *
     * @return  void
     */
    protected function execute(): void
    {
        $task = \Hubzero\Facades\Request::getCmd('task');
        if ($task) {
            if (strstr($task, '.')) {
                $task = explode('.', $task);
                $task = end($task);
                \Hubzero\Facades\Request::setVar('task', $task);
            }
        } else {
            \Hubzero\Facades\Request::setVar('task', \Hubzero\Facades\Request::getCmd('view', 'article'));
        }

        $controller = new Controllers\Articles();
        $controller->execute();
    }
}
