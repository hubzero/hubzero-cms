<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'akismet');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'akismet');
    }
}
