<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Recommendations\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - Recommendations plugin
 **/
class Migration20170831000000PlgPublicationsRecommendations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'recommendations', 0);
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'recommendations');
    }
}
