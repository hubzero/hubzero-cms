<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\EventsLatest\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing events_latest module
 *
*/
class Migration20190109000000ModEventsLatest extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_events_latest');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_events_latest');
    }
}
