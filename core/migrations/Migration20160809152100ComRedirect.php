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
 * Migration script for renaming and changing value format of the column to hub standards
 *
*/
class Migration20160809152100ComRedirect extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__redirect_links', 'updated_date')) {
            $schema->dropIndex('#__redirect_links', 'idx_link_updated');

            $schema->dropColumn('#__redirect_links', 'updated_date');
        }

        if (!$schema->hasColumn('#__redirect_links', 'modified_date')) {
            $schema->addColumn('#__redirect_links', 'modified_date')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();

            $schema->addIndex('#__redirect_links', 'idx_modified_date', 'modified_date');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__redirect_links', 'modified_date')) {
            $schema->dropIndex('#__redirect_links', 'idx_modified_date');

            $schema->dropColumn('#__redirect_links', 'modified_date');
        }

        if (!$schema->hasColumn('#__redirect_links', 'updated_date')) {
            $schema->addColumn('#__redirect_links', 'updated_date')
                ->timestamp()
                ->notNull()
                ->defaultExpression(Expression::currentTimestamp())
                ->execute();

            $schema->addIndex('#__redirect_links', 'idx_link_updated', 'updated_date');
        }
    }
}
