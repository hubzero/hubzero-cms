<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding help component
  *
**/
class Migration20130426071658ComHelp extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addComponentEntry('Help');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteComponentEntry('Help');
    }
}
