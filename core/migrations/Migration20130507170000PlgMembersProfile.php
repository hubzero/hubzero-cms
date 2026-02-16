<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for new member address fields
 *
*/
class Migration20130507170000PlgMembersProfile extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__xprofiles_address')) {
            $schema->createTable('#__xprofiles_address')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('uidNumber')->nullable()
                ->string('addressTo', 200)->nullable()
                ->string('address1', 255)->nullable()
                ->string('address2', 255)->nullable()
                ->string('addressCity', 200)->nullable()
                ->string('addressRegion', 200)->nullable()
                ->string('addressPostal', 200)->nullable()
                ->string('addressCountry', 200)->nullable()
                ->float('addressLatitude')->nullable()
                ->float('addressLongitude')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
