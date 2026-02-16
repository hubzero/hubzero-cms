<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change database engine for support_quest_folders
 *
*/
class Migration20160907105600ComSupport extends Base
{
    public function up()
    {
        $schema->setTableEngine('#__support_query_folders', 'MyISAM');
    }

    public function down()
    {
    }
}
