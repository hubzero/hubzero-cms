<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Hubzilla;

use Hubzero\Module\Module;

/**
 * Module class for displaying the king of hubs
 */
class Hubzilla extends Module
{
    /**
     * Display module
     *
     * @return  void
     */
    public function display()
    {
        $this->renderLayout($this->getLayoutPath($this->params->get('layout', 'default')));
    }
}
