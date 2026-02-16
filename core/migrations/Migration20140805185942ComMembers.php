<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding fulltext index to xprofiles giveName, middleName, and surname fields.
 *
*/
class Migration20140805185942ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $schema->addFulltextIndex('#__xprofiles', 'jos_xprofiles_fullname_ftidx', [
            'givenName',
            'middleName',
            'surname',
        ]);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__xprofiles', 'jos_xprofiles_fullname_ftidx');
    }
}
