<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding new weekly bar chart plugin (time reports)
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20160302141138PlgTimeWeeklybar extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('time', 'weeklybar', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('time', 'weeklybar');
    }
}
