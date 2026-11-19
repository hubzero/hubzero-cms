<?php

namespace Plugins\Authfactors\Google;

use Hubzero\Plugin\Plugin;
use Hubzero\Auth\Factor;
use OTPHP\TOTP;
use Hubzero\Facades\App;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Notify;
use Hubzero\Facades\Request;

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
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var  boolean
     */
    protected $autoloadLanguage = true;

    /**
     * @var  \Hubzero\Plugin\View|null
     */
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
        // Marking the factor enrolled hides the QR code for good, so only the
        // enrollment form (which carries the token) may do it
        Request::checkToken();

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

        // The library that was replaced accepted the previous and the next
        // period as well as this one, so a phone a few seconds off, or a code
        // read just before the window turned, still worked. otphp's third
        // argument is a leeway in seconds, not periods, and 1 shrank that to a
        // one-second window. Check the three periods explicitly instead.
        $now          = time();
        $period       = $totp->getPeriod();
        $verification = false;

        // No code entered is no match
        if ($entered_code !== null && $entered_code !== '') {
            foreach ([-1, 0, 1] as $offset) {
                if ($totp->verify($entered_code, max(0, $now + ($offset * $period)), 0)) {
                    $verification = true;
                    break;
                }
            }
        }

        if ($verification) {
            App::get('session')->set('authfactors.status', true);
        } else {
            Notify::error(Lang::txt('PLG_AUTHFACTORS_GOOGLE_INVALID_CODE'));
        }

        App::redirect(Request::current());
    }
}
