<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Admin\Controllers;

use Components\Oaipmh\Models\Service;
use Hubzero\Component\AdminController;

/**
 * Controller class for OAIPMH config
 */
class Config extends AdminController
{
    /**
     * Display config optins
     *
     * @return  void
     */
    public function displayTask()
    {
        // display panel
        $this->view->display();
    }

    /**
     * Display available schemas
     *
     * @return  void
     */
    public function schemasTask()
    {

        // display panel
        $this->view
            ->set('service', new Service())
            ->display();
    }
}
