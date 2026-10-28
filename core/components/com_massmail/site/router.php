<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Site;

use Hubzero\Component\Router\Base;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Build the route for the component.
     *
     * @param   array  &$query  An array of URL arguments
     * @return  array
     */
    public function build(&$query)
    {
        $segments = array();

        if (!empty($query['task'])) {
            $segments[] = $query['task'];
            unset($query['task']);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array  &$segments  The segments of the URL to parse.
     * @return  array
     */
    public function parse(&$segments)
    {
        $vars = array();

        if (!empty($segments[0])) {
            $vars['task'] = $segments[0];
        }

        return $vars;
    }
}
