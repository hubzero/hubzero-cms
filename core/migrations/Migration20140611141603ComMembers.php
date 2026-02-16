<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding ORCID field to profiles
 *
*/
class Migration20140611141603ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            if (!$schema->hasColumn('#__xprofiles', 'orcid')) {
                $schema->addColumn('#__xprofiles', 'orcid')
                    ->string(255)
                    ->notNull()
                    ->default('')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__xprofiles')) {
            if ($schema->hasColumn('#__xprofiles', 'orcid')) {
                $schema->dropColumn('#__xprofiles', 'orcid');
            }
        }
    }
}
