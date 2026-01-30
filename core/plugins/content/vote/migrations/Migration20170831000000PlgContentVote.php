<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Vote\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Content - Vote plugin
 *
**/
class Migration20170831000000PlgContentVote extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('content', 'vote');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('content', 'vote');
    }
}
