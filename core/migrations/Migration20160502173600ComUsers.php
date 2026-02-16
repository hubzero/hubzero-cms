<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving homeDirectory, loginShell, and ftpShell columns to users table
 *
*/
class Migration20160502173600ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            !$schema->hasColumn('#__users', 'homeDirectory')
            && $schema->hasColumn('#__xprofiles', 'homeDirectory')
        ) {
            $schema->addColumn('#__users', 'homeDirectory')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->set(['u.homeDirectory' => \Hubzero\Database\Expression::column('x.homeDirectory')])
                ->execute();
        }

        if (
            !$schema->hasColumn('#__users', 'loginShell')
            && $schema->hasColumn('#__xprofiles', 'loginShell')
        ) {
            $schema->addColumn('#__users', 'loginShell')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->set(['u.loginShell' => \Hubzero\Database\Expression::column('x.loginShell')])
                ->execute();
        }

        if (!$schema->hasColumn('#__users', 'ftpShell') && $schema->hasColumn('#__xprofiles', 'ftpShell')) {
            $schema->addColumn('#__users', 'ftpShell')->string()->notNull()->execute();

            $this->db->getQuery(true)
                ->update('#__users AS u')
                ->leftJoin('#__xprofiles AS x', 'u.id', 'x.uidNumber')
                ->set(['u.ftpShell' => \Hubzero\Database\Expression::column('x.ftpShell')])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__users', 'homeDirectory')) {
            $schema->dropColumn('#__users', 'homeDirectory');
        }

        if ($schema->hasColumn('#__users', 'loginShell')) {
            $schema->dropColumn('#__users', 'loginShell');
        }

        if ($schema->hasColumn('#__users', 'ftpShell')) {
            $schema->dropColumn('#__users', 'ftpShell');
        }
    }
}
