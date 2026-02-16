<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for renaming 'template' column of newsletters table
 *
*/
class Migration20170314214609ComNewsletters extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__newsletters')) {
            if ($schema->hasColumn('#__newsletters', 'template')) {
                $schema->renameColumn('#__newsletters', 'template', 'template_id')
                    ->integer()
                    ->notNull()
                    ->default(0)
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

        if ($schema->tableExists('#__newsletters')) {
            if ($schema->hasColumn('#__newsletters', 'template_id')) {
                $schema->renameColumn('#__newsletters', 'template_id', 'template')
                    ->integer()
                    ->notNull()
                    ->default(0)
                    ->execute();
            }
        }
    }
}
