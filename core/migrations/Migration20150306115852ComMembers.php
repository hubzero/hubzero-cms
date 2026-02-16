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
 * Migration script for removing bogus characters from names
**/
class Migration20150306115852ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['name' => Expression::replace('name', Expression::hexLiteral('c2ad'), '')])
                ->execute();
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['givenName' => Expression::replace('givenName', Expression::hexLiteral('c2ad'), '')])
                ->execute();
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['middleName' => Expression::replace('middleName', Expression::hexLiteral('c2ad'), '')])
                ->execute();
            $this->db->getQuery(true)
                ->update('#__xprofiles')
                ->set(['surname' => Expression::replace('surname', Expression::hexLiteral('c2ad'), '')])
                ->execute();
        }
        if ($schema->tableExists('#__users')) {
            $this->db->getQuery(true)
                ->update('#__users')
                ->set(['name' => Expression::replace('name', Expression::hexLiteral('c2ad'), '')])
                ->execute();
        }
    }
}
