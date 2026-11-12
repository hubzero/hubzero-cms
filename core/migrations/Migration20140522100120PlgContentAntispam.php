<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for for adding antispam plugins for Akismet, Mollom, and SpamAssassin
**/
class Migration20140522100120PlgContentAntispam extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'akismet', 0);
        $this->addPluginEntry('content', 'mollom', 0);
        $this->addPluginEntry('content', 'spamassassin', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'akismet');
        $this->deletePluginEntry('content', 'mollom');
        $this->deletePluginEntry('content', 'spamassassin');
    }
}
