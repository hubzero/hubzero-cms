<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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

        // Get the toolbar instance
        $bar = \Hubzero\Facades\Toolbar::getRoot();

        // Legacy HTML output (for non-Blade templates)
        $toolbar = $bar->render();

        // Structured button data (for Blade templates)
        $buttons = $bar->getButtons();

        // Get the view
        $path = $this->getLayoutPath($this->params->get('layout', 'default'));

        $this->renderLayout($path, [
            'toolbar' => $toolbar,
            'buttons' => $buttons,
        ]);
    }
}
