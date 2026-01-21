<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for csv time plugin
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20141106184927PlgTimeCsv extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('time', 'csv', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('time', 'csv');
    }
}
