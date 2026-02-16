<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding protected and private access levels
**/
class Migration20150216135336LibJoomla extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__viewlevels')) {
            $query = $this->db->getQuery(true)
                ->select('ordering')
                ->from('#__viewlevels')
                ->order('ordering', 'DESC');

            $i = (int) $query->value('ordering');

            if ($i == 2) {
                foreach (array('Protected' => '[1]', 'Private' => '[8]') as $title => $usergroups) {
                    $i++;

                    $this->db->getQuery(true)
                        ->insert('#__viewlevels')
                        ->set([
                            'id'       => null,
                            'title'    => $title,
                            'ordering' => $i,
                            'rules'    => $usergroups
                        ])
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__viewlevels')) {
            $this->db->getQuery(true)
                ->delete('#__viewlevels')
                ->whereIn('title', ['Protected', 'Private'])
                ->execute();
        }
    }
}
