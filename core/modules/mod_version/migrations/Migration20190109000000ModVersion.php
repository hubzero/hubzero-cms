<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing version module
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 */
class Migration20190109000000ModVersion extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addModuleEntry('mod_version', 1, '', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_version');
    }
}
