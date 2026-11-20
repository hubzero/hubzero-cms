<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\Facades\Route;
use Hubzero\Facades\App;

/**
 * Publication administrative support
 */
class Admin extends AdminController
{
    /**
     * List available admin tasks
     *
     * @return  void
     */
    public function displayTask()
    {
        // Redirect to Publication Manager for now
        App::redirect(
            Route::url('index.php?option=' . $this->option . '&controller=items', false),
        );
    }
}
