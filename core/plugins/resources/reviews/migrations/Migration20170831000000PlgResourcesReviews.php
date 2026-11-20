<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Reviews\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Reviews plugin
 **/
class Migration20170831000000PlgResourcesReviews extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'reviews');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'reviews');
    }
}
