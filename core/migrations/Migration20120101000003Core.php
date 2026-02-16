<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for 2011/12 drop tables
**/
class Migration20120101000003Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('user_map')) {
            $this->db->schema()->dropTable('user_map');
        }

        if ($schema->tableExists('summary_user')) {
            $this->db->schema()->dropTable('summary_user');
        }

        if ($schema->tableExists('summary_simusage_vals')) {
            $this->db->schema()->dropTable('summary_simusage_vals');
        }

        if ($schema->tableExists('summary_simusage')) {
            $this->db->schema()->dropTable('summary_simusage');
        }

        if ($schema->tableExists('summary_misc_vals')) {
            $this->db->schema()->dropTable('summary_misc_vals');
        }

        if ($schema->tableExists('summary_misc')) {
            $this->db->schema()->dropTable('summary_misc');
        }

        if ($schema->tableExists('summary_andmore_vals')) {
            $this->db->schema()->dropTable('summary_andmore_vals');
        }

        if ($schema->tableExists('summary_andmore')) {
            $this->db->schema()->dropTable('summary_andmore');
        }

        if ($schema->tableExists('orgtypes')) {
            $this->db->schema()->dropTable('orgtypes');
        }

        if ($schema->tableExists('#__polls') && $schema->tableExists('#__xpolls')) {
            $this->db->schema()->dropTable('#__polls');
        }

        if ($schema->tableExists('#__poll_menu') && $schema->tableExists('#__xpoll_menu')) {
            $this->db->schema()->dropTable('#__poll_menu');
        }

        if ($schema->tableExists('#__poll_date') && $schema->tableExists('#__xpoll_date')) {
            $this->db->schema()->dropTable('#__poll_date');
        }

        if ($schema->tableExists('#__poll_data') && $schema->tableExists('#__xpoll_data')) {
            $this->db->schema()->dropTable('#__poll_data');
        }

        /* Removing this as xgroups_modules is a needed table. Perhaps it was repurposed at some point?
        if ($schema->tableExists('#__xgroups_modules'))
        {
            $this->db->schema()->dropTable('#__xgroups_modules');
        }*/

        if ($schema->tableExists('#__xforum')) {
            $this->db->schema()->dropTable('#__xforum');
        }

        if ($schema->tableExists('#__modifications')) {
            $this->db->schema()->dropTable('#__modifications');
        }

        if ($schema->tableExists('ipusers')) {
            $this->db->schema()->dropTable('ipusers');
        }
    }
}
