<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Support\Markdown\Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for installing Markdown parser for support comments
 **/
class Migration20190503162652PlgSupportMarkdown extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addPluginEntry('support', 'markdown', 0);
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->deletePluginEntry('support', 'markdown');
    }
}
