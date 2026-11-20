<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Newsletter\Resource\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Newsletter - Resource plugin
 **/
class Migration20170831000000PlgNewsletterResource extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('newsletter', 'resource');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('newsletter', 'resource');
    }
}
