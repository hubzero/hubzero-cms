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
 * Migration script for adding Cache update plugin
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20151216124223PlgUpdateCache extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('update', 'cache');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('update', 'cache');
    }
}
