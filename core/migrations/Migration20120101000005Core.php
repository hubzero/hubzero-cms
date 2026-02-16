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
 * Migration script for 2011/12 views
  *
**/
class Migration20120101000005Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__resource_contributors_view')) {
            $selectQuery = $this->db->getQuery(true)
                ->select('m.uidNumber', 'uidNumber')
                ->select(Expression::count('AA.authorid'), 'count')
                ->from('#__xprofiles', 'm')
                ->join('#__author_assoc AS AA', function ($join) {
                    $join->on('AA.authorid', '=', 'm.uidNumber')
                         ->where('AA.subtable', '=', 'resources');
                }, 'left')
                ->join('#__resources AS R', function ($join) {
                    $join->on('R.id', '=', 'AA.subid')
                         ->where('R.published', '=', 1)
                         ->where('R.standalone', '=', 1);
                }, 'inner')
                ->where('m.public', '=', 1)
                ->group('m.uidNumber');

            $schema->createView('#__resource_contributors_view')
                ->algorithm('UNDEFINED')
                ->definer('CURRENT_USER')
                ->security('INVOKER')
                ->as($selectQuery->toSql('select'));
        }

        if (!$schema->tableExists('#__wiki_contributors_view')) {
            $likePattern = Expression::concat(
                Expression::literal('%'),
                Expression::column('m.username'),
                Expression::literal('%')
            );

            $selectQuery = $this->db->getQuery(true)
                ->select('m.uidNumber', 'uidNumber')
                ->select(Expression::count('w.id'), 'count')
                ->from('#__xprofiles', 'm')
                ->join('#__wiki_page AS w', function ($join) use ($likePattern) {
                    $join->on('w.access', '<>', 1)
                         ->where(function ($where) use ($likePattern) {
                             $where->where('w.created_by', '=', Expression::column('m.uidNumber'))
                                   ->orWhere(function ($where) use ($likePattern) {
                                       $where->where('m.username', '<>', '')
                                             ->where('w.authors', 'LIKE', $likePattern);
                                   });
                         });
                }, 'left')
                ->where('m.public', '=', 1)
                ->whereNotNull('w.id')
                ->group('m.uidNumber');

            $schema->createView('#__wiki_contributors_view')
                ->algorithm('UNDEFINED')
                ->definer('CURRENT_USER')
                ->security('INVOKER')
                ->as($selectQuery->toSql('select'));
        }

        if (!$schema->tableExists('#__contributor_ids_view')) {
            $unionQuery = $this->db->getQuery(true)
                ->select('uidNumber')
                ->from('#__wiki_contributors_view');

            $selectQuery = $this->db->getQuery(true)
                ->select('uidNumber')
                ->from('#__resource_contributors_view')
                ->union($unionQuery);

            $schema->createView('#__contributor_ids_view')
                ->algorithm('UNDEFINED')
                ->definer('CURRENT_USER')
                ->security('INVOKER')
                ->as($selectQuery->toSql('select'));
        }

        if (!$schema->tableExists('#__contributors_view')) {
            $selectQuery = $this->db->getQuery(true)
                ->select('c.uidNumber', 'uidNumber')
                ->select(Expression::coalesce('r.count', 0), 'resource_count')
                ->select(Expression::coalesce('w.count', 0), 'wiki_count')
                ->select(Expression::ifNull('w.count', 0)->plus(Expression::ifNull('r.count', 0)), 'total_count')
                ->from('#__contributor_ids_view', 'c')
                ->leftJoin('#__resource_contributors_view AS r', 'r.uidNumber', 'c.uidNumber')
                ->leftJoin('#__wiki_contributors_view AS w', 'w.uidNumber', 'c.uidNumber');

            $schema->createView('#__contributors_view')
                ->algorithm('UNDEFINED')
                ->definer('CURRENT_USER')
                ->security('INVOKER')
                ->as($selectQuery->toSql('select'));
        }
    }
}
