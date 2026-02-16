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
 * Migration script for creating courses tables
**/
class Migration20130101000000ComCourses extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__courses')) {
            $schema->createTable('#__courses')
                ->integer('id', ['autoIncrement' => true])
                ->string('alias', 255)->default('')
                ->integer('group_id')->default(0)
                ->string('title', 255)->default('')
                ->tinyInteger('state')->default(0)
                ->tinyInteger('type')->default(0)
                ->tinyInteger('access')->default(0)
                ->text('blurb')
                ->text('description')
                ->string('logo', 255)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->text('params')
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_announcements')) {
            $schema->createTable('#__courses_announcements')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->text('content')->nullable()
                ->tinyInteger('priority')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->integer('section_id')->default(0)
                ->tinyInteger('state')->default(0)
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->index('idx_section_id', 'section_id')
                ->index('idx_created_by', 'created_by')
                ->index('idx_state', 'state')
                ->index('idx_priority', 'priority')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_asset_associations')) {
            $schema->createTable('#__courses_asset_associations')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('asset_id')->default(0)
                ->integer('scope_id')->default(0)
                ->string('scope', 255)->default('asset_group')
                ->integer('ordering')->default(0)
                ->primaryKey('id')
                ->index('idx_asset_id', 'asset_id')
                ->index('idx_scope_id', 'scope_id')
                ->index('idx_scope', 'scope')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_asset_group_types')) {
            $schema->createTable('#__courses_asset_group_types')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 200)->default('')
                ->string('type', 255)->default('')
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_asset_groups')) {
            $schema->createTable('#__courses_asset_groups')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('unit_id')->default(0)
                ->string('alias', 250)
                ->string('title', 255)->default('')
                ->string('description', 255)->default('')
                ->integer('ordering')->default(0)
                ->integer('parent')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->primaryKey('id')
                ->index('idx_unit_id', 'unit_id')
                ->index('idx_created_by', 'created_by')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_asset_views')) {
            $schema->createTable('#__courses_asset_views')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('asset_id')
                ->datetime('viewed')
                ->integer('viewed_by')
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_assets')) {
            $schema->createTable('#__courses_assets')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->default('')
                ->mediumText('content')->nullable()
                ->string('type', 255)->default('')
                ->string('url', 255)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(1)
                ->integer('course_id')->default(0)
                ->primaryKey('id')
                ->index('idx_course_id', 'course_id')
                ->index('idx_created_by', 'created_by')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_answers')) {
            $schema->createTable('#__courses_form_answers')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->tinyInteger('correct')
                ->integer('left_dist')
                ->integer('top_dist')
                ->integer('question_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_deployments')) {
            $schema->createTable('#__courses_form_deployments')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('form_id')
                ->timestamp('start_time')->nullable()
                ->timestamp('end_time')->nullable()
                ->string('results_open', 50)->nullable()
                ->integer('time_limit')->nullable()
                ->string('crumb', 20)
                ->string('results_closed', 50)->nullable()
                ->integer('user_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_questions')) {
            $schema->createTable('#__courses_form_questions')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('page')
                ->integer('version')
                ->timestamp('created')
                ->integer('left_dist')
                ->integer('top_dist')
                ->integer('height')
                ->integer('width')
                ->integer('form_id')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_respondent_progress')) {
            $schema->createTable('#__courses_form_respondent_progress')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('respondent_id')
                ->integer('question_id')
                ->integer('answer_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->uniqueIndex('#__pdf_form_respondent_progress_respondent_id_question_id_uidx', [
                    'respondent_id',
                    'question_id',
                ])
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_respondents')) {
            $schema->createTable('#__courses_form_respondents')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('deployment_id')
                ->integer('user_id')
                ->timestamp('started')->nullable()
                ->timestamp('finished')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_form_responses')) {
            $schema->createTable('#__courses_form_responses')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('respondent_id')
                ->integer('question_id')
                ->integer('answer_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->index('#__pdf_form_respones_respondent_id_idx', 'respondent_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_forms')) {
            $schema->createTable('#__courses_forms')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->text('title')->nullable()
                ->tinyInteger('active')->default(1)
                ->timestamp('created')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_log')) {
            $schema->createTable('#__courses_log')
                ->integer('id', ['autoIncrement' => true])
                ->integer('scope_id')->default(0)
                ->string('scope', 100)->default('')
                ->datetime('timestamp')->default('0000-00-00 00:00:00')
                ->integer('user_id')->default(0)
                ->string('action', 50)->default('')
                ->text('comments')
                ->integer('actor_id')->default(0)
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_members')) {
            $schema->createTable('#__courses_members')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->default(0)
                ->integer('course_id')->default(0)
                ->integer('offering_id')->default(0)
                ->integer('section_id')->default(0)
                ->integer('role_id')->default(0)
                ->mediumText('permissions')
                ->datetime('enrolled')->default('0000-00-00 00:00:00')
                ->tinyInteger('student')->default(0)
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->index('idx_user_id', 'user_id')
                ->index('idx_role_id', 'role_id')
                ->index('idx_section_id', 'section_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_offering_section_dates')) {
            $schema->createTable('#__courses_offering_section_dates')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('section_id')->default(0)
                ->string('scope', 150)->default('')
                ->integer('scope_id')->default(0)
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_offering_sections')) {
            $schema->createTable('#__courses_offering_sections')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->string('alias', 255)->default('')
                ->string('title', 255)->default('')
                ->tinyInteger('state')->default(1)
                ->datetime('start_date')->default('0000-00-00 00:00:00')
                ->datetime('end_date')->default('0000-00-00 00:00:00')
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->index('idx_created_by', 'created_by')
                ->index('idx_state', 'state')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_offerings')) {
            $schema->createTable('#__courses_offerings')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('course_id')->default(0)
                ->string('alias', 255)->default('')
                ->string('title', 255)->default('')
                ->string('term', 255)->default('')
                ->tinyInteger('state')->default(1)
                ->datetime('publish_up')->default('0000-00-00 00:00:00')
                ->datetime('publish_down')->default('0000-00-00 00:00:00')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->primaryKey('id')
                ->index('idx_course_id', 'course_id')
                ->index('idx_state', 'state')
                ->index('idx_created_by', 'created_by')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_page_hits')) {
            $schema->createTable('#__courses_page_hits')
                ->integer('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->integer('page_id')->default(0)
                ->integer('user_id')->default(0)
                ->datetime('datetime')->default('0000-00-00 00:00:00')
                ->string('ip', 15)->default('')
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->index('idx_page_id', 'page_id')
                ->index('idx_user_id', 'user_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_pages')) {
            $schema->createTable('#__courses_pages')
                ->integer('id', ['autoIncrement' => true])
                ->string('offering_id', 100)->default('0')
                ->string('url', 100)->default('')
                ->string('title', 100)->default('')
                ->text('content')
                ->integer('porder')->default(0)
                ->integer('active')->default(0)
                ->string('privacy', 10)->default('')
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_reviews')) {
            $schema->createTable('#__courses_reviews')
                ->integer('id', ['autoIncrement' => true])
                ->integer('course_id')->default(0)
                ->integer('offering_id')->default(0)
                ->decimal('rating', 2, 1)->default('0.0')
                ->text('content')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->tinyInteger('anonymous')->default(0)
                ->integer('parent')->default(0)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('state')->default(0)
                ->integer('positive')->default(0)
                ->integer('negative')->default(0)
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_roles')) {
            $schema->createTable('#__courses_roles')
                ->integer('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->string('alias', 150)
                ->string('title', 150)->default('')
                ->mediumText('permissions')
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_units')) {
            $schema->createTable('#__courses_units')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('offering_id')->default(0)
                ->string('alias', 250)
                ->string('title', 255)->default('')
                ->longText('description')
                ->integer('ordering')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->primaryKey('id')
                ->index('idx_offering_id', 'offering_id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__courses_offering_section_codes')) {
            $schema->createTable('#__courses_offering_section_codes')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('section_id')->default(0)
                ->string('code', 10)->default('')
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('expires')->default('0000-00-00 00:00:00')
                ->datetime('redeemed')->default('0000-00-00 00:00:00')
                ->integer('redeemed_by')->default(0)
                ->primaryKey('id')
                ->engine('MYISAM')
                ->charset('utf8')
                ->execute();
        }

        // Create the view using query builder
        $sub1 = $this->db->getQuery(true)
            ->select(Expression::count(0))
            ->from('#__courses_form_responses', 'frei')
            ->where('frei.respondent_id', '=', Expression::column('fre.respondent_id'))
            ->where('frei.id', '>', Expression::column('fre.id'));

        $sub2 = $this->db->getQuery(true)
            ->select(Expression::countDistinct('frei.question_id'))
            ->from('#__courses_form_responses', 'frei')
            ->where('frei.respondent_id', '=', Expression::column('fre.respondent_id'));

        $selectSql = $this->db->getQuery(true)
            ->select(['fre.id', 'fre.respondent_id', 'fre.question_id', 'fre.answer_id'])
            ->from('#__courses_form_responses', 'fre')
            ->where(Expression::subquery($sub1), '<', Expression::subquery($sub2));

        $schema->createView('#__courses_form_latest_responses_view')
            ->algorithm('UNDEFINED')
            ->definer('CURRENT_USER')
            ->security('DEFINER')
            ->as((string) $selectSql);

        // Add FULLTEXT index separately for SQLite compatibility
        $schema->addFulltextIndex('#__courses', '#__xgroups_cn_description_public_desc_ftidx', [
            'alias',
            'title',
            'blurb',
        ]);

        $this->addPluginEntry('members', 'courses');
        $this->addPluginEntry('courses', 'syllabus');
        $this->addPluginEntry('courses', 'forum');
        $this->addPluginEntry('courses', 'progress');
        $this->addPluginEntry('courses', 'announcements');
        $this->addPluginEntry('courses', 'dashboard');
        $this->addPluginEntry('courses', 'overview');
        $this->addPluginEntry('courses', 'reviews');
        $this->addPluginEntry('courses', 'offerings');
    }

    public function down()
    {
        $schema = $this->db->schema();

        $schema->dropTable('#__courses');
        $schema->dropTable('#__courses_announcements');
        $schema->dropTable('#__courses_asset_associations');
        $schema->dropTable('#__courses_asset_group_types');
        $schema->dropTable('#__courses_asset_groups');
        $schema->dropTable('#__courses_asset_views');
        $schema->dropTable('#__courses_assets');
        $schema->dropTable('#__courses_form_answers');
        $schema->dropTable('#__courses_form_deployments');
        $schema->dropTable('#__courses_form_questions');
        $schema->dropTable('#__courses_form_respondent_progress');
        $schema->dropTable('#__courses_form_respondents');
        $schema->dropTable('#__courses_form_responses');
        $schema->dropTable('#__courses_forms');
        $schema->dropTable('#__courses_log');
        $schema->dropTable('#__courses_members');
        $schema->dropTable('#__courses_offering_section_dates');
        $schema->dropTable('#__courses_offering_sections');
        $schema->dropTable('#__courses_offerings');
        $schema->dropTable('#__courses_page_hits');
        $schema->dropTable('#__courses_pages');
        $schema->dropTable('#__courses_reviews');
        $schema->dropTable('#__courses_roles');
        $schema->dropTable('#__courses_units');
        $schema->dropTable('#__courses_offering_section_codes');

        $schema->dropView('#__courses_form_latest_responses_view', true);

        $this->deletePluginEntry('members', 'courses');
        $this->deletePluginEntry('courses');
    }
}
