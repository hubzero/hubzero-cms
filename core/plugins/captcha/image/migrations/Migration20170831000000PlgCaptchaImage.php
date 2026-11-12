<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Captcha\Image\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Captcha - Image plugin
 **/
class Migration20170831000000PlgCaptchaImage extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('captcha', 'image');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('captcha', 'image');
    }
}
