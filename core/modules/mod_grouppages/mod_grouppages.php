<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Grouppages;

use Hubzero\Module\Module;
use Components\Groups\Models;
use Hubzero\Facades\Component;

/**
 * Module class for showing group pages
 */
class Grouppages extends Module
{
    protected $unapprovedModules;
    protected $unapprovedPages;

    /**
     * Display module contents
     *
     * @return     void
     */
    public function display()
    {
        if (!\Hubzero\Facades\App::isAdmin()) {
            return;
        }

        // include group page archive model

        // include group module archive model

        // get unapproved pages
        $groupModelPageArchive = new Models\Page\Archive();
        $this->unapprovedPages = $groupModelPageArchive->pages('unapproved', array(
            'state' => array(0, 1)
        ), true);

        // get unapproved modules
        $groupModelModuleArchive = new Models\Module\Archive();
        $this->unapprovedModules = $groupModelModuleArchive->modules('unapproved', array(
            'state' => array(0, 1)
        ), true);

        // Get the view
        parent::display();
    }
}
