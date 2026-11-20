<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Whatsnew\Wiki\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Whatsnew - Wiki plugin
 */
class Migration20170831000000PlgWhatsnewWiki extends Base
{
    /**
     * Up
     *
     * @return  void
     */
    public function up()
    {
        $this->addPluginEntry('whatsnew', 'wiki');
    }

    /**
     * Down
     *
     * @return  void
     */
    public function down()
    {
        $this->deletePluginEntry('whatsnew', 'wiki');
    }
}
