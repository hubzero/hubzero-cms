<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for making sure usernames are all lowercase
**/
class Migration20150204181025ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            if ($schema->hasColumn('#__xprofiles', 'username')) {
                // Note: Using Expression::lower() for database-agnostic lowercase conversion
                $this->db->getQuery(true)
                    ->update('#__xprofiles')
                    ->set(['username' => Expression::lower('username')])
                    ->execute();
            }

            if ($schema->hasColumn('#__xprofiles', 'homeDirectory')) {
                $this->db->getQuery(true)
                    ->update('#__xprofiles')
                    ->set(['homeDirectory' => Expression::lower('homeDirectory')])
                    ->execute();
            }
        }

        if ($schema->tableExists('#__users') && $schema->hasColumn('#__users', 'username')) {
            $this->db->getQuery(true)
                ->update('#__users')
                ->set(['username' => Expression::lower('username')])
                ->execute();
        }

        if (
            $schema->tableExists('#__event_registration')
            && $schema->hasColumn('#__event_registration', 'username')
        ) {
            $this->db->getQuery(true)
                ->update('#__event_registration')
                ->set(['username' => Expression::lower('username')])
                ->execute();
        }

        if ($schema->tableExists('#__support_acl_aros') && $schema->hasColumn('#__support_acl_aros', 'alias')) {
            $this->db->getQuery(true)
                ->update('#__support_acl_aros')
                ->set(['alias' => Expression::lower('alias')])
                ->execute();
        }

        if ($schema->tableExists('#__support_tickets') && $schema->hasColumn('#__support_tickets', 'login')) {
            $this->db->getQuery(true)
                ->update('#__support_tickets')
                ->set(['login' => Expression::lower('login')])
                ->execute();
        }

        if ($schema->tableExists('#__tool') && $schema->hasColumn('#__tool', 'team')) {
            $this->db->getQuery(true)
                ->update('#__tool')
                ->set(['team' => Expression::lower('team')])
                ->execute();
        }

        if ($schema->tableExists('#__tool') && $schema->hasColumn('#__tool', 'registered_by')) {
            $this->db->getQuery(true)
                ->update('#__tool')
                ->set(['registered_by' => Expression::lower('registered_by')])
                ->execute();
        }

        if ($schema->tableExists('#__tool_version') && $schema->hasColumn('#__tool_version', 'released_by')) {
            $this->db->getQuery(true)
                ->update('#__tool_version')
                ->set(['released_by' => Expression::lower('released_by')])
                ->execute();
        }
    }
}
