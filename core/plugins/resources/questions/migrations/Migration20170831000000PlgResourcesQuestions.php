<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Resources\Questions\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding Resources - Questions plugin
 **/
class Migration20170831000000PlgResourcesQuestions extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('resources', 'questions');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('resources', 'questions');
    }
}
