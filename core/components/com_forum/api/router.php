<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Api;

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
        $segments = [];

        if (!empty($query['controller'])) {
            $segments[] = $query['controller'];
            unset($query['controller']);
        }

        if (!empty($query['id'])) {
            $segments[] = $query['id'];
            unset($query['id']);
        }

        if (!empty($query['task'])) {
            $segments[] = $query['task'];
            unset($query['task']);
        }

        return $segments;
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array<int, string>  &$segments  The segments of the URL to parse.
     * @return  array<string, mixed>  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        // Threads answer the documented routes; likes and users' categories
        // have controllers of their own. This parsed every first segment as an
        // ID and never named a controller, so each route was "Component not
        // found":
        //  /forum              (POST: create a thread)
        //  /forum/list | sections | categories
        //  /forum/{thread}
        //  /forum/likes/list
        //  /forum/userscategories/create | destroy
        $vars = [];

        $vars['controller'] = 'threads';

        if (empty($segments)) {
            return $vars;
        }

        if (in_array($segments[0], ['likes', 'userscategories'])) {
            $vars['controller'] = $segments[0];
            if (isset($segments[1])) {
                $vars['task'] = $segments[1];
            }

            return $vars;
        }

        if (is_numeric($segments[0])) {
            $vars['thread'] = $segments[0];
            $vars['task'] = $segments[1] ?? 'read';
        } else {
            $vars['task'] = $segments[0];
        }

        return $vars;
    }
}
