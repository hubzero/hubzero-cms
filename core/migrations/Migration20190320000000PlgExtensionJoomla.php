<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing plg_extension_joomla plugin
  *
**/
class Migration20190320000000PlgExtensionJoomla extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->deletePluginEntry('extension', 'joomla');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addPluginEntry('extension', 'joomla', 0);
    }
}
