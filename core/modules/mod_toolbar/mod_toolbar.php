<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Toolbar;

use Hubzero\Module\Module;

/**
 * Module class for displaying component toolbar
 */
class Toolbar extends Module
{
    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!\Hubzero\Facades\App::isAdmin()) {
            return;
        }

        // Get the toolbar.
        $toolbar = \Hubzero\Facades\Toolbar::render();

        // Get the view
        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
