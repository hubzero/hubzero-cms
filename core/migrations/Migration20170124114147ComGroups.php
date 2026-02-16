<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to #__xgroups tables
  *
**/
class Migration20170124114147ComGroups extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xgroups_inviteemails')) {
            $schema->addIndex('#__xgroups_inviteemails', 'idx_gidNumber', 'gidNumber');
        }

        if ($schema->tableExists('#__xgroups_log')) {
            $schema->addIndex('#__xgroups_log', 'idx_gidNumber', 'gidNumber');

            $schema->addIndex('#__xgroups_log', 'idx_userid', 'userid');

            $schema->addIndex('#__xgroups_log', 'idx_actorid', 'actorid');
        }

        if ($schema->tableExists('#__xgroups_memberoption')) {
            $schema->addIndex('#__xgroups_memberoption', 'idx_gidNumber', 'gidNumber');

            $schema->addIndex('#__xgroups_memberoption', 'idx_userid', 'userid');
        }

        if ($schema->tableExists('#__xgroups_modules')) {
            $schema->addIndex('#__xgroups_modules', 'idx_gidNumber', 'gidNumber');

            $schema->addIndex('#__xgroups_modules', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__xgroups_pages')) {
            $schema->addIndex('#__xgroups_pages', 'idx_gidNumber', 'gidNumber');

            $schema->addIndex('#__xgroups_pages', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__xgroups_pages_categories')) {
            $schema->addIndex('#__xgroups_pages_categories', 'idx_gidNumber', 'gidNumber');
        }

        if ($schema->tableExists('#__xgroups_pages_versions')) {
            $schema->addIndex('#__xgroups_pages_versions', 'idx_pageid', 'pageid');

            $schema->addIndex('#__xgroups_pages_versions', 'idx_approved', 'approved');

            $schema->addIndex('#__xgroups_pages_versions', 'idx_scanned', 'scanned');
        }

        if ($schema->tableExists('#__xgroups_reasons')) {
            $schema->addIndex('#__xgroups_reasons', 'idx_gidNumber', 'gidNumber');

            $schema->addIndex('#__xgroups_reasons', 'idx_uidNumber', 'uidNumber');
        }

        if ($schema->tableExists('#__xgroups_roles')) {
            $schema->addIndex('#__xgroups_roles', 'idx_gidNumber', 'gidNumber');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xgroups_inviteemails')) {
            $schema->dropIndex('#__xgroups_inviteemails', 'idx_gidNumber');
        }

        if ($schema->tableExists('#__xgroups_log')) {
            $schema->dropIndex('#__xgroups_log', 'idx_gidNumber');

            $schema->dropIndex('#__xgroups_log', 'idx_userid');

            $schema->dropIndex('#__xgroups_log', 'idx_actorid');
        }

        if ($schema->tableExists('#__xgroups_memberoption')) {
            $schema->dropIndex('#__xgroups_memberoption', 'idx_gidNumber');

            $schema->dropIndex('#__xgroups_memberoption', 'idx_userid');
        }

        if ($schema->tableExists('#__xgroups_modules')) {
            $schema->dropIndex('#__xgroups_modules', 'idx_gidNumber');

            $schema->dropIndex('#__xgroups_modules', 'idx_state');
        }

        if ($schema->tableExists('#__xgroups_pages')) {
            $schema->dropIndex('#__xgroups_pages', 'idx_gidNumber');

            $schema->dropIndex('#__xgroups_pages', 'idx_state');
        }

        if ($schema->tableExists('#__xgroups_pages_categories')) {
            $schema->dropIndex('#__xgroups_pages_categories', 'idx_gidNumber');
        }

        if ($schema->tableExists('#__xgroups_pages_versions')) {
            $schema->dropIndex('#__xgroups_pages_versions', 'idx_pageid');

            $schema->dropIndex('#__xgroups_pages_versions', 'idx_approved');

            $schema->dropIndex('#__xgroups_pages_versions', 'idx_scanned');
        }

        if ($schema->tableExists('#__xgroups_reasons')) {
            $schema->dropIndex('#__xgroups_reasons', 'idx_gidNumber');

            $schema->dropIndex('#__xgroups_reasons', 'idx_uidNumber');
        }

        if ($schema->tableExists('#__xgroups_roles')) {
            $schema->dropIndex('#__xgroups_roles', 'idx_gidNumber');
        }
    }
}
