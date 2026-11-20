<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Handlers\Pdf\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Handlers - PDF plugin
 **/
class Migration20170831000000PlgHandlersPdf extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('handlers', 'pdf');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('handlers', 'pdf');
    }
}
