<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Googlegtag;

use Hubzero\Module\Module;
use Hubzero\Facades\Config;

/**
 * Module class for adding Google Gtag script to a page
 */
class Googlegtag extends Module
{
    /**
     * Display module
     *
     * @return  void
     */
    public function display()
    {
        if (substr(strtolower(Config::get('application_env', 'production')), 0, 10) != 'production') {
            return;
        }

        if (!$this->params->get('trackingID')) {
            return;
        }

        require $this->getLayoutPath($this->params->get('layout', 'default'));
    }
}
