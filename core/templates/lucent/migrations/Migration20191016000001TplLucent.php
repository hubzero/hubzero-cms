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
 * Migration script for adding entry for Template
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20191016000001TplLucent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addTemplateEntry('lucent', 'Lucent, the template that inspires', 0, 1, 0, null, 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteTemplateEntry('lucent', 0);
    }
}
