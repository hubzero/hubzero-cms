<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2022 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Authentication\Globus\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Authentication - Globus plugin
 **/
class Migration20221028075821PlgAuthenticationGlobus extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('authentication', 'globus', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('authentication', 'globus');
    }
}
