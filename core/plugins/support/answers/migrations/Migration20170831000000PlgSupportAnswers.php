<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'answers');
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'answers');
    }
}
