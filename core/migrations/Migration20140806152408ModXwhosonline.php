<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing old mod_xwhosonline module
 *
*/
class Migration20140806152408ModXwhosonline extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deleteModuleEntry('mod_xwhosonline');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addModuleEntry('mod_xwhosonline', 1, '', 1);
    }
}
