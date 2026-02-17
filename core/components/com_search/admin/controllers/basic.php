<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Admin\Controllers;

use Hubzero\Component\AdminController;

/**
 * Search controller class
 */
class Basic extends AdminController
{
    /**
     * Display search form and results (if any)
     *
     * @return  void
     */
    public function displayTask()
    {
        $this->view->display();
    }
}
