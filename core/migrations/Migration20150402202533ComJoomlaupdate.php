<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing com_joomlaupdate
  *
**/
class Migration20150402202533ComJoomlaupdate extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteComponentEntry('joomlaupdate');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addComponentEntry('joomlaupdate');
    }
}
