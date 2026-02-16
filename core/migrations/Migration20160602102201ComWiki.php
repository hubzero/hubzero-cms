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
 * Migration script for updating SQL view with renamed wiki tables
 *
*/
class Migration20160602102201ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Drop existing view if it exists
        $schema->dropView('#__wiki_contributors_view', true);

        // Create updated view with new table names
        $query = $this->db->getQuery(true)
            ->select('m.id', 'uidNumber')
            ->select('w.id', 'count', true)
            ->from('#__users', 'm')
            ->leftJoin('#__wiki_pages AS w', function ($join) {
                $join->where('w.access', '<>', 1)
                    ->on('w.created_by', '=', 'm.id');
            })
            ->leftJoin('#__wiki_authors AS a', function ($join) {
                $join->on('a.page_id', '=', 'w.id')
                    ->on('m.id', '=', 'a.user_id');
            })
            ->where('m.access', '=', 1)
            ->whereNotNull('w.id')
            ->group('m.id');

        $schema->createView('#__wiki_contributors_view')
            ->algorithm('UNDEFINED')
            ->definer('CURRENT_USER')
            ->security('INVOKER')
            ->as($query);
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // Drop the updated view
        $schema->dropView('#__wiki_contributors_view', true);

        // Recreate the old view with original table names
        $query = $this->db->getQuery(true)
            ->select('m.uidNumber', 'uidNumber')
            ->select('w.id', 'count', true)
            ->from('#__xprofiles', 'm')
            ->leftJoin('#__wiki_page AS w', function ($join) {
                $join->where('w.access', '<>', 1)
                    ->group(function ($group) {
                        $group->on('w.created_by', '=', 'm.uidNumber')
                            ->group(function ($nested) {
                                $nested->where('m.username', '<>', '')
                                    ->where(
                                        'w.authors',
                                        'like',
                                        Expression::concat(
                                            Expression::literal('%'),
                                            'm.username',
                                            Expression::literal('%')
                                        )
                                    );
                            }, 'or');
                    });
            })
            ->where('m.public', '=', 1)
            ->whereNotNull('w.id')
            ->group('m.uidNumber');

        $schema->createView('#__wiki_contributors_view')
            ->algorithm('UNDEFINED')
            ->definer('CURRENT_USER')
            ->security('INVOKER')
            ->as($query);
    }
}
