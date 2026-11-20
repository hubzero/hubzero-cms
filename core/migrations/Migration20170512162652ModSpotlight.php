<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for uninstalling mod_spotlight
  *
**/
class Migration20170512162652ModSpotlight extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteModuleEntry('mod_spotlight');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addModuleEntry('mod_spotlight');
    }
}
