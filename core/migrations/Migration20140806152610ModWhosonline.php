<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing old mod_whosonline module
**/
class Migration20140806152610ModWhosonline extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addModuleEntry('mod_whosonline', 1, '', 1);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_whosonline');
    }
}
