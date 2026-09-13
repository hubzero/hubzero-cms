<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * System plugin for Mobile template
 */
namespace Plugins\System\Mobile;

use Hubzero\Plugin\Plugin;
use Hubzero\Facades\App;
use Hubzero\Facades\Request;

class Mobile extends Plugin
{
    /**
     * Method to carry template setting in user session if
     * using the mobile template.
     *
     * @return  void
     */
    public function onAfterDispatch()
    {
        if (!App::isSite()) {
            return;
        }

        $session = App::get('session');
        $tmpl = Request::getCmd('tmpl', '');

        // Only switch to a shell that exists.
        //
        // A shell no template provides is a 404, and this one is remembered in
        // the session - so asking for a mobile template a hub does not have
        // used to cost the visitor not that page but every page after it. A
        // hub without one shows the site it does have.
        $hasMobile = App::get('template.loader')->hasShell('mobile');

        if ($tmpl == 'mobile' && $hasMobile) {
            $session->set('mobile', true);
        } elseif ($tmpl == 'mobile') {
            Request::setVar('tmpl', 'index');
        } elseif ($session->get('mobile')) {
            if ($hasMobile) {
                Request::setVar('tmpl', 'mobile');
            } else {
                // Remembered from a hub, or a template, that had one
                $session->set('mobile', false);
            }
        }

        // Are we requesting to view full site again?
        if ($tmpl == 'fullsite') {
            $session->set('mobile', false);

            Request::setVar('tmpl', 'index');

            // SCRIPT_URI is Apache's, and not every server sets it
            $url = preg_replace('/([?&])tmpl=fullsite(&|$)/', '$1', Request::current(true));

            App::redirect(rtrim($url, '?&'));
        }
    }
}
