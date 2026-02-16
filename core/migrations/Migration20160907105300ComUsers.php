<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change database engine for some user tables
 *
*/
class Migration20160907105300ComUsers extends Base
{
    public function up()
    {
        $schema->setTableEngine('#__users_tool_preferences', 'MyISAM');
        $schema->setTableEngine('#__users_quotas_classes_groups', 'MyISAM');
    }

    public function down()
    {
    }
}
