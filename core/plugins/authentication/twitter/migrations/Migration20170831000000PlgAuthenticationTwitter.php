<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Twitter\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Twitter plugin
 **/
class Migration20170831000000PlgAuthenticationTwitter extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'twitter', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'twitter');
    }
}
