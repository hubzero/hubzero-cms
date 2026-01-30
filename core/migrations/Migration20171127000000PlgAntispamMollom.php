<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Antispam - Mollom plugin
 *
*/
class Migration20171127000000PlgAntispamMollom extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('antispam', 'mollom');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('antispam', 'mollom');
    }
}
