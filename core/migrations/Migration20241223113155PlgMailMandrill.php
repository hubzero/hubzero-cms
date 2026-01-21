<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2024 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing Mail - Mandrill plugin
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20241223113155PlgMailMandrill extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('mail', 'mandrill');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('mail', 'mandrill', 0);
    }
}
