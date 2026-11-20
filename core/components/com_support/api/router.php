<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Api;

use Hubzero\Component\Router\Base;
use Hubzero\Facades\App;

/**
 * Routing class for the component
 */
class Router extends Base
{
    /**
     * Controllers besides tickets that answer /support/{controller}
     *
     * @var  array<int,string>
     */
    private const CONTROLLERS = ['categories', 'comments', 'messages', 'outstandingtickets', 'statuses'];

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
        $vars = [];

        $vars['controller'] = 'tickets';

        // The v2 tickets controller documents its URIs as /support/tickets[/...];
        // the loader already takes the controller from that segment, so drop it
        // here or "tickets" was read as the task (POST /support/tickets and
        // GET /support/tickets/{id} answered "Task [...] not found")
        if (isset($segments[0]) && $segments[0] == 'tickets') {
            array_shift($segments);
        }

        // These have controllers of their own, which every route used to miss
        // (each was taken for a ticket task: "Task [categories] not found"):
        //  /support/{controller}         (POST: create; outstandingtickets: the list)
        //  /support/{controller}/list
        //  /support/{controller}/{id}    (GET: read; PUT/DELETE as the method says)
        if (isset($segments[0]) && in_array($segments[0], self::CONTROLLERS)) {
            $vars['controller'] = $segments[0];
            $method = App::get('request')->method();

            if (isset($segments[1]) && is_numeric($segments[1])) {
                $vars['id'] = $segments[1];
                if ($method == 'GET') {
                    $vars['task'] = 'read';
                }
            } elseif (isset($segments[1])) {
                $vars['task'] = $segments[1];
            } elseif ($method == 'GET' && $segments[0] == 'outstandingtickets') {
                $vars['task'] = 'list';
            }

            return $vars;
        }

        if (isset($segments[0])) {
            if (is_numeric($segments[0])) {
                $vars['id']     = $segments[0];
                $vars['ticket'] = $segments[0]; // the v1 tickets controller reads 'ticket'
                $method = App::get('request')->method();
                switch ($method) {
                    case 'GET':
                        $vars['task'] = 'read';
                        break;
                    case 'PUT':
                        $vars['task'] = 'update';
                        break;
                    case 'DELETE':
                        $vars['task'] = 'delete';
                        break;
                }
            } else {
                $vars['task'] = $segments[0];
            }

            if (isset($segments[1])) {
                if ($segments[1] == 'comments') {
                    // /support/{ticket}/comments[/list|/{comment}]: this always
                    // answered "list", so the documented comment create, read,
                    // update and delete could never be reached
                    $vars['controller'] = $segments[1];
                    $vars['ticket']     = $segments[0];
                    unset($vars['task']);
                    if (isset($segments[2]) && is_numeric($segments[2])) {
                        $vars['comment'] = $segments[2];
                        $method = App::get('request')->method();
                        if ($method == 'GET') {
                            $vars['task'] = 'read';
                        }
                    } elseif (isset($segments[2])) {
                        $vars['task'] = $segments[2];
                    } elseif (App::get('request')->method() == 'GET') {
                        $vars['task'] = 'list';
                    }
                } else {
                    $vars['task'] = $segments[1];
                }
            }
        }

        return $vars;
    }
}
