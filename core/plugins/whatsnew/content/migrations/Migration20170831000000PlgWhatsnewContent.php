<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     */
    public function up()
    {
        $this->addPluginEntry('whatsnew', 'content');
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $this->deletePluginEntry('whatsnew', 'content');
    }
}
