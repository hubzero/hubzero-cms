<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component;

use Hubzero\Facades\Request;

class DefaultSiteController extends SiteController
{
    public function execute()
    {
        $this->_task = strtolower(Request::getCmd('task', ''));

        $this->_taskMap[$this->_task] = $this->_task;

        parent::execute();
    }

    public function __call($name, $arguments)
    {
        if (substr_compare($name, 'Task', -4, 4, true) === 0) {
            $this->view->display();
        } else {
            trigger_error('Call to undefined method ' . get_class($this) . '::' . $name . '()', E_USER_ERROR);
        }
    }
}
