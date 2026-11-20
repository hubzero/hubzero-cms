<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Citation\Doi\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enable or diable plugin DOI for citation component.
 **/
class Migration20190325155621PlgCitationDoi extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('citation', 'doi');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('citation', 'doi');
    }
}
