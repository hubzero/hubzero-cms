<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\FeedYoutube\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing feed_youtube module
 *
*/
class Migration20190109000000ModFeedYoutube extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_feed_youtube');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_feed_youtube');
    }
}
