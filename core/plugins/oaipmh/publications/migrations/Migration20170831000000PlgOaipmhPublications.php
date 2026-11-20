<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Oaipmh\Publications\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Oaipmh - Publications plugin
 *
*/
class Migration20170831000000PlgOaipmhPublications extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('oaipmh', 'publications');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('oaipmh', 'publications');
    }
}
