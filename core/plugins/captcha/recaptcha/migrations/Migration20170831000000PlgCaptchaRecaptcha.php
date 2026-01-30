<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Captcha\Recaptcha\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Captcha - Recaptcha plugin
 **/
class Migration20170831000000PlgCaptchaRecaptcha extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('captcha', 'recaptcha', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('captcha', 'recaptcha');
    }
}
