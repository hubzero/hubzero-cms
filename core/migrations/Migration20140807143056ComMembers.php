<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming previously added members fulltext index on givenName, middleName and surname fields
  *
**/
class Migration20140807143056ComMembers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // drop orignal key and create new one
        if ($schema->tableExists('#__xprofiles')) {
            $schema->dropIndex('#__xprofiles', 'jos_xprofiles_fullname_ftidx');

            if (
                $schema->hasColumn('#__xprofiles', 'givenName')
                && $schema->hasColumn('#__xprofiles', 'middleName')
                && $schema->hasColumn('#__xprofiles', 'surname')
            ) {
                $schema->addFulltextIndex('#__xprofiles', 'ftidx_fullname', ['givenName', 'middleName', 'surname']);
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
            $schema->dropIndex('#__xprofiles', 'ftidx_fullname');

            if (
                $schema->hasColumn('#__xprofiles', 'givenName')
                && $schema->hasColumn('#__xprofiles', 'middleName')
                && $schema->hasColumn('#__xprofiles', 'surname')
            ) {
                $schema->addFulltextIndex('#__xprofiles', 'jos_xprofiles_fullname_ftidx', [
                    'givenName',
                    'middleName',
                    'surname',
                ]);
            }
        }
    }
}
