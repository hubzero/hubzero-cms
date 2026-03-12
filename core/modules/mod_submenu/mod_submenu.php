<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Submenu;

use Hubzero\Module\Module;

/**
 * Module class for rendering a submenu
 */
class Submenu extends Module
{
    protected $list;

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

        $this->list = \Hubzero\Facades\Submenu::getItems();

        if (!is_array($this->list) || !count($this->list)) {
            return;
        }

        $path = $this->getLayoutPath($this->params->get('layout', 'default'));
        $this->renderLayout($path);
    }
}
