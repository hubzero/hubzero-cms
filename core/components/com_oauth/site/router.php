<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oauth\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Build the route for the component.
     *
     * @param   array<string, mixed>  &$query  An array of URL arguments
     * @return  array<int, mixed>  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        return [];
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array<int, string>  &$segments  The segments of the URL to parse.
     * @return  array<string, mixed>  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = [];

        if (empty($segments)) {
            return $vars;
        }

        if (isset($segments[0])) {
            $vars['task'] = $segments[0];
        }

        return $vars;
    }
}
