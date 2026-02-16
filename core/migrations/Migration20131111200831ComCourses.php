<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for courses badges cleanup
  *
**/
class Migration20131111200831ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_offering_badges')
            && !$schema->tableExists('#__courses_offering_section_badges')
        ) {
            $schema->renameTable('#__courses_offering_badges', '#__courses_offering_section_badges');
        } elseif (
            $schema->tableExists('#__courses_offering_badges')
            && $schema->tableExists('#__courses_offering_section_badges')
        ) {
            $schema->dropTable('#__courses_offering_badges');
        }

        if ($schema->hasColumn('#__courses_offerings', 'badge_id')) {
            $schema->dropColumn('#__courses_offerings', 'badge_id');
        }

        if (
            $schema->hasColumn('#__courses_offering_section_badges', 'offering_id')
            && !$schema->hasColumn('#__courses_offering_section_badges', 'section_id')
        ) {
            $schema->renameColumn('#__courses_offering_section_badges', 'offering_id', 'section_id')
                ->integer()
                ->notNull()
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_offering_section_badges', 'provider_name')
            && $schema->hasColumn('#__courses_offering_section_badges', 'section_id')
        ) {
            $schema->addColumn('#__courses_offering_section_badges', 'provider_name')
                ->string(255)
                ->notNull()
                ->default('passport')
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_offering_section_badges', 'provider_badge_id')
            && $schema->hasColumn('#__courses_offering_section_badges', 'badge_id')
        ) {
            $schema->renameColumn('#__courses_offering_section_badges', 'badge_id', 'provider_badge_id')
                ->integer()
                ->notNull()
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_offering_section_badges', 'criteria_id')
            && $schema->hasColumn('#__courses_offering_section_badges', 'img_url')
        ) {
            $schema->addColumn('#__courses_offering_section_badges', 'criteria_id')
                ->integer()
                ->notNull()
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_offering_section_badges', 'published')
            && $schema->hasColumn('#__courses_offering_section_badges', 'section_id')
        ) {
            $schema->addColumn('#__courses_offering_section_badges', 'published')
                ->integer(1)
                ->notNull()
                ->default(0)
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_member_badges', 'section_badge_id')
            && $schema->hasColumn('#__courses_member_badges', 'member_id')
        ) {
            $schema->addColumn('#__courses_member_badges', 'section_badge_id')
                ->integer()
                ->notNull()
                ->execute();
        }

        if ($schema->hasColumn('#__courses_member_badges', 'claim_url')) {
            $schema->dropColumn('#__courses_member_badges', 'claim_url');
        }

        if (
            !$schema->hasColumn('#__courses_member_badges', 'validation_token')
            && $schema->hasColumn('#__courses_member_badges', 'action_on')
        ) {
            $schema->addColumn('#__courses_member_badges', 'validation_token')
                ->string(20)
                ->nullable()
                ->execute();
        }

        if (
            !$schema->hasColumn('#__courses_member_badges', 'criteria_id')
            && $schema->hasColumn('#__courses_member_badges', 'validation_token')
        ) {
            $schema->addColumn('#__courses_member_badges', 'criteria_id')->integer()->nullable()->execute();
        }

        if (!$schema->tableExists('#__courses_offering_section_badge_criteria')) {
            $schema->createTable('#__courses_offering_section_badge_criteria')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->text('text')
                ->integer('section_badge_id')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->execute();
        }
    }
}
