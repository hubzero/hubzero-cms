<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Captcha\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Captcha plugin
 **/
class Migration20170831000000PlgSupportCaptcha extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'captcha');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'captcha');
    }
}
