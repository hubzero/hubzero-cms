<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Referrer Policy plugin
 **/
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20190225000000PlgSystemReferrerpolicy extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'referrerpolicy');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'referrerpolicy');
    }
}
