<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for moving registerDate data from #__xprofiles to #__users
 *
 */
class Migration20170405140417ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__xprofiles')
            && $schema->tableExists('#__users')
            && $schema->hasColumn('#__xprofiles', 'registerDate')
            && $schema->hasColumn('#__users', 'registerDate')
        ) {
            $results = $this->db->getQuery(true)
                ->select(['u.id', 'p.registerDate'])
                ->from('#__users', 'u')
                ->innerJoin('#__xprofiles AS p', 'p.uidNumber', 'u.id')
                ->where('u.registerDate', '=', '0000-00-00 00:00:00')
                ->orWhereIsNull('u.registerDate')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $result) {
                    $this->db->getQuery(true)
                        ->update('#__users')
                        ->set(['registerDate' => $result->registerDate])
                        ->where('id', '=', $result->id)
                        ->execute();
                }
            }
        }
    }
}
