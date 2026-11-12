<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Hubzero\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Hubzero plugin
 **/
class Migration20170831000000PlgAuthenticationHubzero extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'hubzero');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'hubzero');
    }
}
