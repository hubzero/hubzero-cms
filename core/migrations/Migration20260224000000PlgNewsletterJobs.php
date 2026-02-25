<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing Newsletter - Jobs plugin
 *
 * The plugin depended on com_feedaggregator which was removed in 2019.
 * Also removes the feedaggregator plugin entry as a safety net.
 **/
class Migration20260224000000PlgNewsletterJobs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('newsletter', 'jobs');
        $this->deletePluginEntry('newsletter', 'feedaggregator');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('newsletter', 'jobs');
    }
}
