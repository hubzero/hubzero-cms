<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding search projects entry
  *
**/
class Migration20131106150723PlgYsearchProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('ysearch', 'projects');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('ysearch', 'projects');
    }
}
