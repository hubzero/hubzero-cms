<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Site\Controllers;

use Components\Storefront\Models\Warehouse;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Request;

/**
 * Courses controller class
 *
 * Set on the controller as it runs:
 *
 * @property  Warehouse  $warehouse  The storefront's stock, scoped to the visitor by execute()
 */
class Storefront extends ComponentController
{
    /**
     * Execute a task
     *
     * @return  void
     */
    public function execute()
    {
        $this->warehouse = new Warehouse();

        // Get the task
        $this->task  = Request::getCmd('task', '');

        if (empty($this->task)) {
            $this->task = 'home';
            $this->registerTask('__default', $this->task);
        }

        parent::execute();
    }

    /**
     * Display default page
     *
     * @return  void
     */
    public function homeTask()
    {
        // get categories
        $categories = $this->warehouse->getRootCategories();
        $this->view->set('categories', $categories);

        $this->view->set('config', $this->config);

        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->option)),
                'index.php?option=' . $this->option,
            );
        }

        $this->view->display();
    }
}
