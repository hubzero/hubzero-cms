<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Akismet\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Akismet plugin
 */
class Migration20170831000000PlgAntispamAkismet extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'akismet');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'akismet');
    }
}
