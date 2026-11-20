<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Answers\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Support - Answers plugin
 *
 **/
class Migration20170831000000PlgSupportAnswers extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'answers');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'answers');
    }
}
