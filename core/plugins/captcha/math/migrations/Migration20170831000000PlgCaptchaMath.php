<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('captcha', 'math', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('captcha', 'math');
    }
}
