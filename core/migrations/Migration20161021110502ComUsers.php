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
 * Migration script for syncing loginShell and ftpShell between users and xprofiles
 *
*/
class Migration20161021110502ComUsers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__users') && $schema->tableExists('#__xprofiles')) {
            $this->db->getQuery(true)
                ->update('#__users', 'u')
                ->innerJoin('#__xprofiles AS x', 'x.uidNumber', 'u.id')
                ->set(['u.loginShell' => Expression::column('x.loginShell')])
                ->whereColumn('u.loginShell', '!=', 'x.loginShell')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__users', 'u')
                ->innerJoin('#__xprofiles AS x', 'x.uidNumber', 'u.id')
                ->set(['u.ftpShell' => Expression::column('x.ftpShell')])
                ->whereColumn('u.ftpShell', '!=', 'x.ftpShell')
                ->execute();
        }
    }
}
