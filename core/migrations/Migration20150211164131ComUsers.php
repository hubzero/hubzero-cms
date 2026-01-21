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
 * Migration script for adding new pending users module
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20150211164131ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addModuleEntry('mod_users', 0, '', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_users', 1);
    }
}
