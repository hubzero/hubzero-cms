<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding System - Spamjail plugin
 **/
// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
class Migration20170831000000PlgSystemSpamjail extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('system', 'spamjail', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('system', 'spamjail');
    }
}
