<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming the column to Relational standards
 *
*/
class Migration20160610231100ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wiki_versions', 'pageid')) {
            $schema->renameColumn('#__wiki_versions', 'pageid', 'page_id')->integer()->notNull()->default(0)->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wiki_versions', 'page_id')) {
            $schema->renameColumn('#__wiki_versions', 'page_id', 'pageid')->integer()->notNull()->default(0)->execute();
        }
    }
}
