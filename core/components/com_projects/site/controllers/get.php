<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Projects\Tables;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;
use Hubzero\Facades\App;

/**
 * Projects Public links controller class
 */
class Get extends SiteController
{
    /**
     * Pub view for project files, notes etc.
     *
     * @return  mixed
     */
    public function displayTask()
    {

        $route = Route::url('index.php?option=' . $this->option, false);

        // Incoming
        $stamp = Request::getString('s', '');

        // Clean up stamp value (only numbers and letters)
        $regex  = ['/[^a-zA-Z0-9]/'];
        $stamp  = preg_replace($regex, '', $stamp);

        // Load item reference
        $objSt = new Tables\Stamp($this->database);
        if (!$stamp || !$objSt->loadItem($stamp)) {
            App::redirect(
                $route,
            );
        }

        // Can only serve files or notes at the moment
        if (!in_array($objSt->type, ['files', 'notes', 'publications'])) {
            App::redirect(
                $route,
            );
        }

        // Serve requested item
        $content = Event::trigger('projects.serve', [$objSt->type, $objSt->projectid, $objSt->reference]);

        // Output
        foreach ($content as $out) {
            if ($out) {
                return $out;
            }
        }

        // Redirect if nothing fetched
        App::redirect(
            $route,
        );
    }
}
