<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for 2011/12 create table statements
**/
class Migration20120101000001Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__author_role_types')) {
            $schema->createTable('#__author_role_types')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('role_id')->default(0)
                ->integer('type_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__author_roles')) {
            $schema->createTable('#__author_roles')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->nullable()
                ->string('alias', 255)->nullable()
                ->tinyInteger('state')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__billboard_collection')) {
            $schema->createTable('#__billboard_collection')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('name', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__billboards')) {
            $schema->createTable('#__billboards')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('collection_id')->nullable()
                ->string('name', 255)->nullable()
                ->string('header', 255)->nullable()
                ->text('text')->nullable()
                ->string('learn_more_text', 255)->nullable()
                ->string('learn_more_target', 255)->nullable()
                ->string('learn_more_class', 255)->nullable()
                ->string('learn_more_location', 255)->nullable()
                ->string('background_img', 255)->nullable()
                ->string('padding', 255)->nullable()
                ->string('alias', 255)->nullable()
                ->text('css')->nullable()
                ->tinyInteger('published')->default(0)
                ->integer('ordering')->nullable()
                ->integer('checked_out')->default(0)
                ->datetime('checked_out_time')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__citations_sponsors')) {
            $schema->createTable('#__citations_sponsors')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('sponsor', 150)->nullable()
                ->string('link', 200)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__citations_sponsors_assoc')) {
            $schema->createTable('#__citations_sponsors_assoc')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('cid')->nullable()
                ->integer('sid')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__citations_types')) {
            $schema->createTable('#__citations_types')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 255)->nullable()
                ->string('type_title', 255)->nullable()
                ->text('type_desc')->nullable()
                ->string('type_export', 255)->nullable()
                ->text('fields')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__document_resource_rel')) {
            $schema->createTable('#__document_resource_rel')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('document_id')
                ->integer('resource_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->uniqueIndex('jos_document_resource_rel_document_id_resource_id_uidx', ['document_id', 'resource_id'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__document_text_data')) {
            $schema->createTable('#__document_text_data')
                ->integer('id', ['autoIncrement' => true])
                ->text('body')->nullable()
                ->char('hash', 40)
                ->primaryKey('id')
                ->uniqueIndex('jos_document_text_data_hash_uidx', 'hash')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Add FULLTEXT index separately for SQLite compatibility
            $schema->addFulltextIndex('#__document_text_data', 'jos_document_text_data_body_ftidx', 'body');
        }

        if (!$schema->tableExists('#__focus_area_resource_type_rel')) {
            $schema->createTable('#__focus_area_resource_type_rel')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('focus_area_id')
                ->integer('resource_type_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__focus_areas')) {
            $schema->createTable('#__focus_areas')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('tag_id')
                ->integer('mandatory_depth')->nullable()
                ->integer('multiple_depth')->nullable()
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__forum_attachments')) {
            $schema->createTable('#__forum_attachments')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('parent')->default(0)
                ->integer('post_id')->default(0)
                ->string('filename', 255)->nullable()
                ->string('description', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__forum_categories')) {
            $schema->createTable('#__forum_categories')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->nullable()
                ->string('alias', 255)->nullable()
                ->text('description')->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('state')->default(0)
                ->integer('group_id')->default(0)
                ->integer('section_id')->default(0)
                ->tinyInteger('closed')->default(0)
                ->integer('asset_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__forum_posts')) {
            $schema->createTable('#__forum_posts')
                ->integer('id', ['autoIncrement' => true])
                ->integer('category_id')->default(0)
                ->string('title', 255)->nullable()
                ->text('comment')->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('state')->default(0)
                ->tinyInteger('sticky')->default(0)
                ->integer('parent')->default(0)
                ->integer('hits')->default(0)
                ->integer('group_id')->default(0)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('anonymous')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->datetime('last_activity')->default('0000-00-00 00:00:00')
                ->integer('asset_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            // Add FULLTEXT indexes separately for SQLite compatibility
            $schema->addFulltextIndex('#__forum_posts', 'question', 'comment');
            $schema->addFulltextIndex('#__forum_posts', 'comment_title_fidx', ['comment', 'title']);
        }

        if (!$schema->tableExists('#__forum_sections')) {
            $schema->createTable('#__forum_sections')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('title', 255)->nullable()
                ->string('alias', 255)->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->tinyInteger('access')->default(0)
                ->tinyInteger('state')->default(0)
                ->integer('group_id')->default(0)
                ->integer('asset_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__incremental_registration_group_label_rel')) {
            $schema->createTable('#__incremental_registration_group_label_rel')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('group_id')
                ->integer('label_id')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__incremental_registration_groups')) {
            $schema->createTable('#__incremental_registration_groups')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->integer('hours')
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__incremental_registration_labels')) {
            $schema->createTable('#__incremental_registration_labels')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->string('field', 50)
                ->string('label', 100)
                ->primaryKey('id')
                ->uniqueIndex('id', 'id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__incremental_registration_options')) {
            $schema->createTable('#__incremental_registration_options')
                ->timestamp('added')
                ->text('popover_text')
                ->integer('award_per')
                ->integer('test_group')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__incremental_registration_popover_recurrence')) {
            $schema->createTable('#__incremental_registration_popover_recurrence')
                ->integer('idx')
                ->integer('hours')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__metrics_ipgeo_cache')) {
            $schema->createTable('#__metrics_ipgeo_cache')
                ->integer('ip')->default(0)
                ->char('countrySHORT')->length(2)->default('')
                ->string('countryLONG')->length(64)->default('')
                ->string('ipREGION')->length(128)->default('')
                ->string('ipCITY')->length(128)->default('')
                ->double('ipLATITUDE')->precision(16)->scale(4)->nullable()
                ->double('ipLONGITUDE')->precision(16)->scale(4)->nullable()
                ->timestamp('lookup_datetime')
                ->primaryKey('ip')
                ->index('lookup_datetime', 'lookup_datetime')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__oauthp_consumers')) {
            $schema->createTable('#__oauthp_consumers')
                ->integer('id', ['autoIncrement' => true])
                ->tinyInteger('state')
                ->string('token', 250)
                ->string('secret', 250)
                ->string('callback_url', 250)
                ->tinyInteger('xauth')
                ->tinyInteger('xauth_grant')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__oauthp_nonces')) {
            $schema->createTable('#__oauthp_nonces')
                ->integer('id', ['autoIncrement' => true])
                ->string('nonce', 250)
                ->integer('stamp')
                ->datetime('created')
                ->primaryKey('id')
                ->uniqueIndex('unonce', ['nonce', 'stamp'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__oauthp_tokens')) {
            $schema->createTable('#__oauthp_tokens')
                ->integer('id', ['autoIncrement' => true])
                ->integer('consumer_id')
                ->integer('user_id')
                ->tinyInteger('state')
                ->string('token', 250)
                ->string('token_secret', 250)
                ->string('callback_url', 250)
                ->string('verifier', 250)
                ->datetime('created')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__profile_completion_awards')) {
            $schema->createTable('#__profile_completion_awards')
                ->integer('user_id')
                ->tinyInteger('name')->default(0)
                ->tinyInteger('orgtype')->default(0)
                ->tinyInteger('organization')->default(0)
                ->tinyInteger('countryresident')->default(0)
                ->tinyInteger('countryorigin')->default(0)
                ->tinyInteger('gender')->default(0)
                ->tinyInteger('url')->default(0)
                ->tinyInteger('reason')->default(0)
                ->tinyInteger('race')->default(0)
                ->tinyInteger('phone')->default(0)
                ->tinyInteger('picture')->default(0)
                ->tinyInteger('opted_out')->default(0)
                ->integer('logins')->default(1)
                ->integer('invocations')->default(0)
                ->timestamp('last_bothered')
                ->integer('bothered_times')->default(0)
                ->tinyInteger('edited_profile')->default(0)
                ->primaryKey('user_id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__recommendation')) {
            $schema->createTable('#__recommendation')
                ->integer('fromID')
                ->integer('toID')
                ->float('contentScore')->nullable()
                ->float('tagScore')->nullable()
                ->float('titleScore')->nullable()
                ->timestamp('timestamp')
                ->primaryKey(['fromID', 'toID'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__resource_licenses')) {
            $schema->createTable('#__resource_licenses')
                ->integer('id', ['autoIncrement' => true])
                ->string('name', 100)->nullable()
                ->text('text')->nullable()
                ->string('title', 100)->nullable()
                ->integer('ordering')->default(0)
                ->tinyInteger('apps_only')->default(0)
                ->string('main', 255)->nullable()
                ->string('icon', 255)->nullable()
                ->string('url', 255)->nullable()
                ->tinyInteger('agreement')->default(0)
                ->text('info')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__resource_sponsors')) {
            $schema->createTable('#__resource_sponsors')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('alias', 255)->nullable()
                ->string('title', 255)->nullable()
                ->tinyInteger('state')->default(1)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('created_by')->default(0)
                ->datetime('modified')->default('0000-00-00 00:00:00')
                ->integer('modified_by')->default(0)
                ->text('description')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__resource_stats_clusters')) {
            $schema->createTable('#__resource_stats_clusters')
                ->unsignedBigInteger('id', ['autoIncrement' => true])
                ->string('cluster', 255)->default('')
                ->string('username', 32)->default('')
                ->integer('uidNumber')->default(0)
                ->string('toolname', 80)->default('')
                ->integer('resid')->default(0)
                ->string('clustersize', 255)->default('')
                ->datetime('cluster_start')->default('0000-00-00 00:00:00')
                ->datetime('cluster_end')->default('0000-00-00 00:00:00')
                ->string('institution', 255)->default('')
                ->timestamp('timestamp')
                ->primaryKey('id')
                ->index('cluster', 'cluster')
                ->index('username', 'username')
                ->index('uidNumber', 'uidNumber')
                ->index('toolname', 'toolname')
                ->index('resid', 'resid')
                ->index('clustersize', 'clustersize')
                ->index('cluster_start', 'cluster_start')
                ->index('cluster_end', 'cluster_end')
                ->index('institution', 'institution')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__session_geo')) {
            $schema->createTable('#__session_geo')
                ->string('session_id', 200)->default('0')
                ->string('username', 150)->default('')
                ->string('time', 14)->default('')
                ->tinyInteger('guest')->default(1)
                ->integer('userid')->default(0)
                ->string('ip', 15)->nullable()
                ->string('host', 128)->nullable()
                ->string('domain', 128)->nullable()
                ->tinyInteger('signed')->default(0)
                ->char('countrySHORT')->length(2)->nullable()
                ->string('countryLONG')->length(64)->nullable()
                ->string('ipREGION')->length(128)->nullable()
                ->string('ipCITY')->length(128)->nullable()
                ->double('ipLATITUDE')->precision(16)->scale(4)->nullable()
                ->double('ipLONGITUDE')->precision(16)->scale(4)->nullable()
                ->tinyInteger('bot')->default(0)
                ->primaryKey('session_id')
                ->index('userid', 'userid')
                ->index('time', 'time')
                ->index('ip', 'ip')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__session_log')) {
            $schema->createTable('#__session_log')
                ->integer('id', ['autoIncrement' => true])
                ->tinyInteger('clientid')->nullable()
                ->char('session_id', 64)->nullable()
                ->char('psid', 64)->nullable()
                ->char('rsid', 64)->nullable()
                ->char('ssid', 64)->nullable()
                ->integer('user_id')->nullable()
                ->char('authenticator', 64)->nullable()
                ->char('source', 64)->nullable()
                ->char('ip', 64)->nullable()
                ->datetime('created')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__support_queries')) {
            $schema->createTable('#__support_queries')
                ->integer('id', ['autoIncrement' => true])
                ->string('title', 250)->nullable()
                ->text('conditions')->nullable()
                ->text('query')->nullable()
                ->integer('user_id')->default(0)
                ->string('sort', 100)->nullable()
                ->string('sort_dir', 100)->nullable()
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->integer('iscore')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__tags_log')) {
            $schema->createTable('#__tags_log')
                ->integer('id', ['autoIncrement' => true])
                ->integer('tag_id')->default(0)
                ->datetime('timestamp')->default('0000-00-00 00:00:00')
                ->integer('user_id')->default(0)
                ->string('action', 50)->nullable()
                ->text('comments')->nullable()
                ->integer('actorid')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__tags_substitute')) {
            $schema->createTable('#__tags_substitute')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('tag_id')->default(0)
                ->string('tag', 100)->nullable()
                ->string('raw_tag', 100)->nullable()
                ->integer('created_by')->default(0)
                ->datetime('created')->default('0000-00-00 00:00:00')
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__user_roles')) {
            $schema->createTable('#__user_roles')
                ->integer('user_id')
                ->string('role', 20)
                ->integer('group_id')->nullable()
                ->integer('id', ['autoIncrement' => true])
                ->primaryKey('id')
                ->uniqueIndex('uidx_role_user_id_group_id', ['role', 'user_id', 'group_id'])
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__wiki_page_author')) {
            $schema->createTable('#__wiki_page_author')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->integer('user_id')->default(0)
                ->integer('page_id')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__wiki_page_metrics')) {
            $schema->createTable('#__wiki_page_metrics')
                ->integer('pageid')->default(0)
                ->string('pagename', 100)->nullable()
                ->integer('hits')->default(0)
                ->integer('visitors')->default(0)
                ->integer('visits')->default(0)
                ->primaryKey('pageid')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__xgroups_memberoption')) {
            $schema->createTable('#__xgroups_memberoption')
                ->integer('id', ['autoIncrement' => true])
                ->integer('gidNumber')->nullable()
                ->integer('userid')->nullable()
                ->string('optionname', 100)->nullable()
                ->string('optionvalue', 100)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__xgroups_pages_hits')) {
            $schema->createTable('#__xgroups_pages_hits')
                ->integer('id', ['autoIncrement' => true])
                ->integer('gid')->nullable()
                ->integer('pid')->nullable()
                ->integer('uid')->nullable()
                ->datetime('datetime')->nullable()
                ->string('ip', 15)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__xorganization_types')) {
            $schema->createTable('#__xorganization_types')
                ->unsignedInteger('id', ['autoIncrement' => true])
                ->string('type', 150)->nullable()
                ->string('title', 255)->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
