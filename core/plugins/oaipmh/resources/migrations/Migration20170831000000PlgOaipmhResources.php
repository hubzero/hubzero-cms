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
 * Migration script for adding Oaipmh - Resources plugin
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20170831000000PlgOaipmhResources extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('oaipmh', 'resources');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('oaipmh', 'resources');
    }
}
