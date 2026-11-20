<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Session\Helper as SessionHelper;

/**
 * Manage members password blacklist
 */
class WhosOnline extends AdminController
{
    /**
     * Display whose online
     *
     * @return     void
     */
    public function displayTask()
    {
        // get all sessions
        $this->view->set('rows', SessionHelper::getAllSessions([
            'guest'    => 0,
            'distinct' => 1,
        ]));

        // Output the HTML
        $this->view->display();
    }
}
