<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
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
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropIndex('#__xprofiles', 'jos_xprofiles_fullname_ftidx');
    }
}
