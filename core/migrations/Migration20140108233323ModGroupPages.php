<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for group pages module
 *
*/
class Migration20140108233323ModGroupPages extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_grouppages');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_grouppages');
    }
}
