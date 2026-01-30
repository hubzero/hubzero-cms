<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $this->addPluginEntry('antispam', 'spamassassin');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('antispam', 'spamassassin');
    }
}
