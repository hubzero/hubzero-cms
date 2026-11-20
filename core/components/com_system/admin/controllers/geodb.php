<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;

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
