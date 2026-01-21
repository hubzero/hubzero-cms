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
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 *
 * Migration script for adding entry for Search - Kb plugin
 **/
class Migration20170831000000PlgSearchKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('search', 'kb');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('search', 'kb');
    }
}
