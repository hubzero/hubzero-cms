<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Spamassassin\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Antispam - Spamassassin plugin
 **/
class Migration20170831000000PlgAntispamSpamassassin extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'spamassassin');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'spamassassin');
    }
}
