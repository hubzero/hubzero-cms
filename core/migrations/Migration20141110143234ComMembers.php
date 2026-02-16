<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding default member organization types
 *
*/
class Migration20141110143234ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xorganization_types')) {
            $count = $this->db->getQuery(true)
                ->from('#__xorganization_types')
                ->count();

            if (!$count) {
                $types = array(
                    'universityundergraduate' => 'University / College Undergraduate',
                    'universitygraduate'      => 'University / College Graduate Student',
                    'universityfaculty'       => 'University / College Faculty',
                    'universitystaff'         => 'University / College Staff',
                    'precollegestudent'       => 'K-12 (Pre-College) Student',
                    'precollegefacultystaff'  => 'K-12 (Pre-College) Faculty/Staff',
                    'nationallab'             => 'National Laboratory',
                    'industry'                => 'Industry / Private Company',
                    'government'              => 'Government Agency',
                    'military'                => 'Military',
                    'unemployed'              => 'Retired / Unemployed'
                );

                foreach ($types as $alias => $title) {
                    $this->db->getQuery(true)
                        ->insert('#__xorganization_types')
                        ->columns(['type', 'title'])
                        ->values([$alias, $title])
                        ->execute();
                }
            }
        }
    }
}
