<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Grouppages;

use Hubzero\Module\Module;
use Components\Groups\Models;

/**
 * Module class for showing group pages
 */
class Grouppages extends Module
{
    /**
     * @var  mixed
     */
    protected $unapprovedModules;
    /**
     * @var  mixed
     */
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
        $this->unapprovedPages = $groupModelPageArchive->pages('unapproved', [
            'state' => [0, 1],
        ], true);

        // get unapproved modules
        $groupModelModuleArchive = new Models\Module\Archive();
        $this->unapprovedModules = $groupModelModuleArchive->modules('unapproved', [
            'state' => [0, 1],
        ], true);

        // Get the view
        parent::display();
    }
}
