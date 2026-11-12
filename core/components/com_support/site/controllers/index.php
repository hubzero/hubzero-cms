<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Hubzero\Component\SiteController;
use Hubzero\Facades\Document;
use Hubzero\Facades\Pathway;
use Hubzero\Facades\Lang;

/**
 * Displays support pages
 */
class Index extends SiteController
{
    /**
     * Displays the main page for support
     *
     * @return  void
     */
    public function displayTask()
    {
        // Set the page title
        $title = Lang::txt(strtoupper($this->option));

        Document::setTitle($title);
        $this->view->set('title', $title);

        // Set the pathway
        if (Pathway::count() <= 0) {
            Pathway::append(
                Lang::txt(strtoupper($this->option)),
                'index.php?option=' . $this->option,
            );
        }

        // Output HTML
        $this->view->display();
    }
}
