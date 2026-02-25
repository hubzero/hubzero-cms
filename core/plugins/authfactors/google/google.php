<?php

namespace Plugins\Authfactors\Google;

use Hubzero\Plugin\Plugin;
use Hubzero\Auth\Factor;
use OTPHP\TOTP;
use Hubzero\Facades\Request;
use Hubzero\Facades\App;
use Hubzero\Facades\Notify;

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Factor Auth plugin for TOTP-based identity verification
 */
class Google extends Plugin
{
    protected $view;

    /**
     * Renders the auth factor challenge
     *
     * @return string
     **/
    public function onRenderChallenge()
    {
        $response = new \Hubzero\Base\Obj();

        switch (Request::getWord('action', '')) {
            case 'registered':
                $this->register();
                break;
            case 'verify':
                $this->verify();
                break;
            default:
                $this->display();
                break;
        }

        $response->set('html', $this->view->loadTemplate());

        return $response;
    }

    /**
     * Displays the appropriate page for user input
     *
     * @return void
     **/
    private function display()
    {
        if (Factor::currentOrFailByEnrolled()) {
            $this->view = $this->view('verify', 'challenge');
        } else {
            $this->view = $this->view('enroll', 'challenge');
        }
    }

    /**
     * Registers a new user
     *
     * @return void
     **/
    private function register()
    {
        Factor::registerUserAsEnrolled();
        App::redirect(Request::current());
    }

    /**
     * Verifies the incoming token against the current user
     *
     * @return void
     **/
    private function verify()
    {
        $data = json_decode(Factor::currentOrFailByDomain('google')->data);
        $entered_code = Request::getString('token');

        $totp = TOTP::createFromSecret($data->secret);
        $verification = $totp->verify($entered_code, null, 1);

        if ($verification) {
            App::get('session')->set('authfactors.status', true);
        } else {
            Notify::error($verification);
        }

        App::redirect(Request::current());
    }
}
