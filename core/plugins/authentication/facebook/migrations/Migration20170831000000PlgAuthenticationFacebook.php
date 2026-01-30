<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Facebook\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Facebook plugin
 **/
class Migration20170831000000PlgAuthenticationFacebook extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'facebook', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'facebook');
    }
}
