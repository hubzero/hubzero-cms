<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing courses references to user_id that should really be member_id
  *
**/
class Migration20130905195600ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_grade_book', 'user_id')) {
            // Fix gradebook entires
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__courses_grade_book')
                ->order('user_id', 'ASC')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    switch ($r->scope) {
                        case 'asset':
                            $course_id = $this->db->getQuery(true)
                                ->select('course_id')
                                ->from('#__courses_assets')
                                ->where('id', '=', $r->scope_id)
                                ->value('course_id');
                            break;

                        case 'unit':
                            $course_id = $this->db->getQuery(true)
                                ->select('course_id')
                                ->from('#__courses_units', 'cu')
                                ->innerJoin('#__courses_offerings AS co', 'cu.offering_id', 'co.id')
                                ->where('cu.id', '=', $r->scope_id)
                                ->value('course_id');
                            break;

                        case 'course':
                            $course_id = $r->scope_id;
                            break;
                    }

                    $id = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__courses_members')
                        ->where('user_id', '=', $r->user_id)
                        ->where('course_id', '=', $course_id)
                        ->order('student', 'DESC')
                        ->order('first_visit', 'DESC')
                        ->value('id');

                    if ($id) {
                        $this->db->getQuery(true)
                            ->update('#__courses_grade_book')
                            ->set(['user_id' => $id])
                            ->where('id', '=', $r->id)
                            ->execute();
                    } else {
                        $this->db->getQuery(true)
                            ->delete('#__courses_grade_book')
                            ->where('id', '=', $r->id)
                            ->execute();
                    }
                }
            }
        }

        if (
            $schema->hasColumn('#__courses_grade_book', 'user_id')
            && !$schema->hasColumn('#__courses_grade_book', 'member_id')
        ) {
            $schema->renameColumn('#__courses_grade_book', 'user_id', 'member_id', 'INT(11) NOT NULL');
        }

        // Fix old asset views data that doesn't have course_id filled in...
        $results = $this->db->getQuery(true)
            ->select('asset_id')
            ->group('asset_id')
            ->from('#__courses_asset_views')
            ->whereIsNull('course_id')
            ->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $r) {
                $id = $this->db->getQuery(true)
                    ->select('course_id')
                    ->from('#__courses_assets')
                    ->where('id', '=', $r->asset_id)
                    ->value('course_id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__courses_asset_views')
                        ->set(['course_id' => $id])
                        ->where('asset_id', '=', $r->asset_id)
                        ->execute();
                }
            }
        }

        // Fix asset views
        $results = $this->db->getQuery(true)
            ->select('*')
            ->from('#__courses_asset_views')
            ->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $r) {
                $id = $this->db->getQuery(true)
                    ->select('id')
                    ->from('#__courses_members')
                    ->where('user_id', '=', $r->viewed_by)
                    ->where('course_id', '=', $r->course_id)
                    ->order('student', 'DESC')
                    ->order('first_visit', 'DESC')
                    ->value('id');

                if ($id) {
                    $this->db->getQuery(true)
                        ->update('#__courses_asset_views')
                        ->set(['viewed_by' => $id])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }

        if ($schema->hasColumn('#__courses_form_respondents', 'user_id')) {
            // Fix form respondents
            $results = $this->db->getQuery(true)
                ->select('*')
                ->from('#__courses_form_respondents')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $course_id = $this->db->getQuery(true)
                        ->select('ca.course_id')
                        ->from('#__courses_form_respondents', 'cfr')
                        ->innerJoin('#__courses_form_deployments AS cfd', 'cfr.deployment_id', 'cfd.id')
                        ->innerJoin('#__courses_forms AS cf', 'cfd.form_id', 'cf.id')
                        ->innerJoin('#__courses_assets AS ca', 'cf.asset_id', 'ca.id')
                        ->where('cfr.id', '=', $r->id)
                        ->value('ca.course_id');

                    $id = $this->db->getQuery(true)
                        ->select('id')
                        ->from('#__courses_members')
                        ->where('user_id', '=', $r->user_id)
                        ->where('course_id', '=', $course_id)
                        ->order('student', 'DESC')
                        ->order('first_visit', 'DESC')
                        ->value('id');

                    $this->db->getQuery(true)
                        ->update('#__courses_form_respondents')
                        ->set(['user_id' => $id])
                        ->where('id', '=', $r->id)
                        ->execute();
                }
            }
        }

        if (
            $schema->hasColumn('#__courses_form_respondents', 'user_id')
            && !$schema->hasColumn('#__courses_form_respondents', 'member_id')
        ) {
            $schema->renameColumn('#__courses_form_respondents', 'user_id', 'member_id', 'INT(11) NOT NULL');
        }
    }
}
