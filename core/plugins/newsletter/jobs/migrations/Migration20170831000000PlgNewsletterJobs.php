<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Jobs\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Newsletter - Jobs plugin
 **/
class Migration20170831000000PlgNewsletterJobs extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('newsletter', 'jobs');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('newsletter', 'jobs');
    }
}
