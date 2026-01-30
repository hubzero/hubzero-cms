<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Content\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Whatsnew - Content plugin
 */
class Migration20170831000000PlgWhatsnewContent extends Base
{
    /**
     * Up
     */
    public function up()
    {
        $this->addPluginEntry('whatsnew', 'content');
    }

    /**
     * Down
     */
    public function down()
    {
        $this->deletePluginEntry('whatsnew', 'content');
    }
}
