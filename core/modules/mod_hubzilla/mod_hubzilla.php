<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
