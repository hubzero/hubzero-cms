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

        // Asked for the full site again
        if ($tmpl == 'fullsite') {
            $session->set('mobile', false);

            Request::setVar('tmpl', 'index');

            // SCRIPT_URI is Apache's, and not every server sets it
            $url = preg_replace('/([?&])tmpl=fullsite(&|$)/', '$1', Request::current(true));

            App::redirect(rtrim($url, '?&'));

            return;
        }

        // Every other request is only this plugin's business if the mobile
        // template has been asked for, now or earlier in the session.
        if ($tmpl != 'mobile' && !$session->get('mobile')) {
            return;
        }

        // Only switch to a shell that exists.
        //
        // A shell no template provides is a 404, and this one is remembered
        // for the session - so asking for a mobile template a hub does not
        // have used to cost the visitor not that page but every page after
        // it. A hub without one forgets the request and shows the site it
        // does have.
        if (!App::get('template.loader')->hasShell('mobile')) {
            $session->set('mobile', false);

            if ($tmpl == 'mobile') {
                Request::setVar('tmpl', 'index');
            }

            return;
        }

        if ($tmpl == 'mobile') {
            $session->set('mobile', true);
        } else {
            Request::setVar('tmpl', 'mobile');
        }
    }
}
