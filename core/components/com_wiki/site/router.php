<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Site;

use Hubzero\Component\Router\Base;
use Hubzero\Facades\Request;

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

        if (isset($query['scope'])) {
            unset($query['scope']);
        }

        if (!empty($query['pagename'])) {
            $segments[] = $query['pagename'];
        }
        unset($query['pagename']);

        unset($query['controller']);

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

        if (empty($segments)) {
            return $vars;
        }

        $vars['pagename'] = end($segments);

        $vars['task'] = Request::getWord('task', '');

        switch ($vars['task']) {
            case 'upload':
            case 'download':
            case 'deletefolder':
            case 'deletefile':
            case 'media':
            case 'list':
                $vars['controller'] = 'media';
                break;

            case 'history':
            case 'compare':
            case 'approve':
            case 'deleterevision':
                // the history view links "set as current" on the site wiki too; the
                // group plugin already routes it to the history controller
            case 'setcurrentrevision':
                $vars['controller'] = 'history';
                break;

            case 'editcomment':
            case 'addcomment':
            case 'savecomment':
            case 'reportcomment':
            case 'removecomment':
            case 'comments':
                $vars['controller'] = 'comments';
                break;

            case 'delete':
            case 'edit':
            case 'save':
            case 'rename':
            case 'saverename':
            default:
                $vars['controller'] = 'page';
                break;
        }

        if (
            substr(strtolower($vars['pagename']), 0, strlen('image:')) == 'image:'
            || substr(strtolower($vars['pagename']), 0, strlen('file:')) == 'file:'
        ) {
            $vars['controller'] = 'media';
            $vars['task'] = 'download';
        }

        $vars['pagename'] = implode('/', $segments);

        return $vars;
    }
}
