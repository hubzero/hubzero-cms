<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Mail - Mandrill plugin
 *
*/
class Migration20241223113155PlgMailMandrill extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('mail', 'mandrill');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('mail', 'mandrill', 0);
    }
}
