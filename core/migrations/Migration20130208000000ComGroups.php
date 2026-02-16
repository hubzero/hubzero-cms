<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for privacy/access cleanup in xgroups table
**/
class Migration20130208000000ComGroups extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__xgroups', 'access')) {
            $schema->dropColumn('#__xgroups', 'access');
        }
        if (
            $schema->hasColumn('#__xgroups', 'privacy')
            && !$schema->hasColumn('#__xgroups', 'discoverability')
        ) {
            $schema->renameColumn('#__xgroups', 'privacy', 'discoverability')->tinyInteger(3);
        }
        if (!$schema->hasColumn('#__xgroups', 'approved')) {
            $schema->addColumn('#__xgroups', 'approved')->tinyInteger(3)->default(1)->after('published');
        }
    }

    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__xgroups', 'approved')) {
            $schema->dropColumn('#__xgroups', 'approved');
        }
        if (
            !$schema->hasColumn('#__xgroups', 'privacy')
            && $schema->hasColumn('#__xgroups', 'discoverability')
        ) {
            $schema->renameColumn('#__xgroups', 'discoverability', 'privacy')->tinyInteger(3);
        }
        if (!$schema->hasColumn('#__xgroups', 'access')) {
            $schema->addColumn('#__xgroups', 'access')->tinyInteger(3)->default(0)->after('type');
        }
    }
}
