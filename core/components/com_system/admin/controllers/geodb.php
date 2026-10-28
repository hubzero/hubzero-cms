<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

/**
 * System controller class for info
 */
class Geodb extends AdminController
{
    /**
     * Default view
     *
     * @return  void
     */
    public function displayTask()
    {
        // Output the HTML
        $this->view->display();
    }
}
