<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Jsonld\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding entry for Publications - JSON-LD plugin
 **/
class Migration20180914000000PlgPublicationsJsonld extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('publications', 'jsonld');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('publications', 'jsonld');
    }
}
