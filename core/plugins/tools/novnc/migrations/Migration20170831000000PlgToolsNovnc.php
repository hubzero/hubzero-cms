<?php

// phpcs:disable PSR1.Files.SideEffects


// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Tools - NoVNC plugin
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20170831000000PlgToolsNovnc extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('tools', 'novnc');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('tools', 'novnc');
    }
}
