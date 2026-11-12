<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for remove old mod_logged module
 *
*/
class Migration20140806131000ModLogged extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteModuleEntry('mod_logged');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addModuleEntry('mod_logged', 1, '', 1);
    }
}
