<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Captcha\Math\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Captcha - Math plugin
 **/
class Migration20170831000000PlgCaptchaMath extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('captcha', 'math', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('captcha', 'math');
    }
}
