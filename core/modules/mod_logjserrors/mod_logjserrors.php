<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Logjserrors;

use Hubzero\Module\Module;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;

/**
 * Module class for logging JS errors
 */
class Logjserrors extends Module
{
    /**
     * Display module content
     *
     * @return  void
     */
    public function display()
    {
        if (Request::method() == 'POST') {
            // Write to hubzero logs if available.
            // Otherwise fallback to the app logs.
            $path = Config::get('log_path');
            if (is_dir('/var/log/hubzero')) {
                $path = '/var/log/hubzero';
            }

            // A page re-rendered after some other form's POST runs this module
            // too, so only a report carrying every field the script below sends
            // is treated as one; anything else just renders the script.
            $log = [];
            foreach (['message', 'file', 'line', 'url', 'navigator'] as $k) {
                if (!isset($_POST[$k]) || !is_scalar($_POST[$k])) {
                    $log = null;
                    break;
                }
                $log[$k] = substr((string) $_POST[$k], 0, 1024);
            }

            if ($log !== null) {
                file_put_contents($path . '/client_error.log', (string) json_encode($log), FILE_APPEND);
                exit();
            }
        }

        require $this->getLayoutPath();
    }
}
