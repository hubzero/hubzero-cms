<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Api\Controllers;

use Hubzero\Content\Server;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Config;

/**
 * API controller for the time component
 */
class Formv1r0 extends base
{
    /**
     * Gets form images
     *
     * @apiMethod GET
     * @apiUri    /courses/form/image
     * @apiParameter {
     *      "name":        "id",
     *      "description": "Form ID",
     *      "type":        "integer",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "form_version",
     *      "description": "Form version number",
     *      "type":        "integer",
     *      "required":    false,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "file",
     *      "description": "Image filename",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @apiParameter {
     *      "name":        "token",
     *      "description": "Session authentication token",
     *      "type":        "string",
     *      "required":    true,
     *      "default":     null
     * }
     * @return    void
     */
    public function imageTask()
    {
        $id       = Request::getInt('id', 0);
        $version  = Request::getInt('form_version', 0);
        $filename = Request::getString('file', '');
        $filename = urldecode($filename);
        $filename = PATH_APP . DS . 'site' . DS . 'courses' . DS . 'forms' . DS . $id . DS
            . (($version) ? $version . DS : '') . ltrim($filename, DS);

        // Ensure the file exist
        if (!file_exists($filename)) {
            // Return message
            App::abort(404, 'Image not found');
        }

        // Add silly simple security check
        $token      = Request::getString('token', false);
        $session_id = App::get('session')->getId();
        $secret     = Config::get('secret');
        $hash       = hash('sha256', $session_id . ':' . $secret);

        if ($token !== $hash) {
            App::abort(401, 'You don\'t have permission to do this');
        }

        // Initiate a new content server and serve up the file
        header("HTTP/1.1 200 OK");
        $xserver = new Server();
        $xserver->filename($filename);
        $xserver->disposition('inline');
        $xserver->acceptranges(false);

        if (!$xserver->serve()) {
            // Return message
            App::abort(500, 'Failed to serve the image');
        }
    }
}
