<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Recommendations\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Recommendations plugin
 **/
class Migration20170831000000PlgResourcesRecommendations extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'recommendations', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'recommendations');
    }
}
