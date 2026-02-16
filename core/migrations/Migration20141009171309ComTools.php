<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing fields in a few middleware tables
 *
*/
class Migration20141009171309ComTools extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$mwdb = $this->getMWDBO()) {
            $this->setError('Failed to connect to the middleware database', 'warning');
            return false;
        }

        $schema = $mwdb->schema();

        if ($schema->tableExists('host') && $schema->hasColumn('host', 'venue_id')) {
            $schema->dropColumn('host', 'venue_id');
        }

        if ($schema->tableExists('zones') && $schema->hasColumn('zones', 'picture')) {
            $schema->dropColumn('zones', 'picture');
        }

        // Note: Middleware database is typically MySQL-only, so CHANGE COLUMN is kept as-is
        // If middleware ever supports SQLite, this would need a helper method
        if (
            $schema->tableExists('zones')
            && $schema->hasColumn('zones', 'title')
            && $schema->hasColumn('zones', 'zone')
        ) {
            $mwdb->schema()->table('zones')->alter()
                ->modifyColumn('title')
                ->string(255)
                ->nullable()
                ->default(null)
                ->after('zone')
                ->execute();
        }
    }
}
