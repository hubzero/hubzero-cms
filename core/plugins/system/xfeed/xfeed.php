<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin to force .rss and .atom URLs to raw document mode
 */

namespace Plugins\System\Xfeed;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

class Xfeed extends Plugin
{
    /**
     * Perform actions after initialization
     *
     * @return  void
     */
    public function onAfterInitialise()
    {
        if (!App::isSite()) {
            return;
        }

        $uri = Request::getString('REQUEST_URI', null, 'server');
        $bits = explode('?', (string) $uri);
        $bit = $bits[0];

        if (!strpos($bit, '.')) {
            return;
        }

        $bi = explode('.', $bit);
        $b = end($bi);
        $b = strtolower($b);

        if ($b == 'rss' || $b == 'atom') {
            Request::setVar('no_html', 1);
            Request::setVar('format', 'raw');
        }
    }
}
