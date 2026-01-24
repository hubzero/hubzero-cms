<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace

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
