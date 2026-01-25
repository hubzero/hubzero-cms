<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Component\Router;

class DefaultRouter extends Base
{
    /**
     * Build the route for the component.
     *
     * @param   array  &$query  An array of URL arguments
     * @return  array  The URL arguments to use to assemble the subsequent URL.
     */
    public function build(&$query)
    {
        $segments = array();
        if (
            isset($query['controller']) &&
            preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", (string) $query['controller']) == 1
        ) {
            $segments[] = (string) $query['controller'];
            unset($query['controller']);
            if (
                isset($query['task']) &&
                preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", (string) $query['task']) == 1
            ) {
                $segments[] = (string) $query['task'];
                unset($query['task']);
                if (
                    isset($query['id']) &&
                    preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", (string) $query['id']) == 1
                ) {
                    $segments[] = (string) $query['id'];
                    unset($query['id']);
                }
            }
        }

        return array();
    }

    /**
     * Parse the segments of a URL.
     *
     * @param   array  &$segments  The segments of the URL to parse.
     * @return  array  The URL attributes to be used by the application.
     */
    public function parse(&$segments)
    {
        $vars = array();

        if (isset($segments[0])) {
            $vars['controller'] = (string) $segments[0];
        }

        if (isset($segments[1])) {
            $vars['task'] = (string) $segments[1];
        }

        if (isset($segments[2])) {
            $vars['id'] = (string) $segments[2];
        }

        return $vars;
    }
}
