<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formatwiki\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Formatwiki plugin
 *
**/
class Migration20170831000000PlgContentFormatwiki extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'formatwiki', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'formatwiki');
    }
}
