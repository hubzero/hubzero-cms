<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Submenu;

use Hubzero\Module\Module;

/**
 * Module class for rendering a submenu
 */
class Submenu extends Module
{
    /**
     * Get the items of the submenu and display them.
     *
     * @return  void
     */
    public function display()
    {
        if (!\Hubzero\Facades\App::isAdmin() || !class_exists('\\Submenu')) {
            return;
        }

        // Initialise variables.
        $list = \Hubzero\Facades\Submenu::getItems();

        if (!count($list)) {
            return;
        }

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
