<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\Eprivacy;

use Hubzero\Module\Module;
use Hubzero\Facades\App;
use Hubzero\Facades\User;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Config;

/**
 * Module class for site activity
 */
class Eprivacy extends Module
{
    protected $duration;
    protected $message;
    protected $moduleid;
    protected $uri;

    /**
     * Display module contents
     *
     * @return  void
     */
    public function display()
    {
        if (!App::isSite() || !User::isGuest()) {
            return;
        }

        if (User::getState($this->module->module)) {
            return;
        }

        $this->moduleid = $this->params->get('moduleid', 'eprivacy');
        $this->duration = $this->params->get('duration', 365);

        // Get current unix timestamp
        $now = time() + (Config::get('offset') * 60 * 60);

        $expires = $now + 60 * 60 * 24 * intval($this->duration);

        $hide = Request::cookie($this->moduleid, '');

        if (!$hide && Request::getVar($this->moduleid, '', 'get')) {
            setcookie($this->moduleid, 'acknowledged', $expires);
            return;
        }

        if ($hide) {
            return;
        }

        $defaultMessage = Lang::txt('MOD_EPRIVACY_DEFAULT_MESSAGE', Config::get('sitename'));
        $this->message  = $this->params->get('message', $defaultMessage);

        $uri  = Request::getString('REQUEST_URI', '', 'server');
        $uri .= (strstr($uri, '?')) ? '&' : '?';
        $uri .= $this->moduleid . '=close';

        $this->uri = $uri;

        // Get the view
        parent::display();
    }
}
