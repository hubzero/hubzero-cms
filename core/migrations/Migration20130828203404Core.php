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
 * Migration script for updating view security to be invoker
 *
 * On MySQL, views are created with ALGORITHM=UNDEFINED, DEFINER=CURRENT_USER,
 * and SQL SECURITY INVOKER for proper permission handling.
 * On SQLite, views are simply recreated (no security concepts).
 **/
class Migration20130828203404Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // View 1: contributor_ids_view - union of resource and wiki contributors
        try {
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
        } catch (\Exception $e) {
            // View may not exist if dependent views don't exist
        }

        // View 2: contributors_view - aggregates resource and wiki counts
        try {
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
        } catch (\Exception $e) {
            // View may not exist if dependent views don't exist
        }

        // View 3: courses_form_latest_responses_view - latest form responses
        try {
            $subquery1 = $this->db->getQuery(true)
                ->select(Expression::count(0))
                ->from('#__courses_form_responses', 'frei')
                ->where('frei.respondent_id', '=', Expression::column('fre.respondent_id'))
                ->where('frei.id', '>', Expression::column('fre.id'));

            $subquery2 = $this->db->getQuery(true)
                ->select(Expression::countDistinct('frei.question_id'))
                ->from('#__courses_form_responses', 'frei')
                ->where('frei.respondent_id', '=', Expression::column('fre.respondent_id'));

            $selectQuery = $this->db->getQuery(true)
                ->select(['fre.id', 'fre.respondent_id', 'fre.question_id', 'fre.answer_id'])
                ->from('#__courses_form_responses', 'fre')
                ->where($subquery1, '<', $subquery2);

            $schema->createView('#__courses_form_latest_responses_view')
                ->algorithm('UNDEFINED')
                ->definer('CURRENT_USER')
                ->security('INVOKER')
                ->as($selectQuery->toSql('select'));
        } catch (\Exception $e) {
            // View may not exist if table doesn't exist
        }

        // View 4: resource_contributors_view - counts published resources per user
        try {
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
        } catch (\Exception $e) {
            // View may not exist if tables don't exist
        }

        // View 5: wiki_contributors_view - counts wiki pages per user
        // Uses sqlConcat for cross-database CONCAT compatibility
        try {
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
        } catch (\Exception $e) {
            // View may not exist if tables don't exist
        }
    }
}
