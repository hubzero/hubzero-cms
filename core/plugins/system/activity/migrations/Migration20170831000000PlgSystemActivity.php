<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Activity plugin
 **/
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20170831000000PlgSystemActivity extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'activity');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'activity');
    }
}
