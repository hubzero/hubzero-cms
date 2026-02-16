<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for 2011/12 table modifications
 *
*/
class Migration20120101000004Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__blog_entries')) {
            $schema->addFulltextIndex('#__blog_entries', 'ftidx_title_content', ['title', 'content']);
        }

        if ($schema->tableExists('#__citations')) {
            if (
                $schema->hasColumn('#__citations', 'type')
                && $schema->hasColumn('#__citations', 'uid')
            ) {
                $schema->modifyColumn('#__citations', 'type')->string(30)->nullable()->execute();
            }

            if (
                $schema->hasColumn('#__citations', 'published')
                && $schema->hasColumn('#__citations', 'type')
            ) {
                $schema->modifyColumn('#__citations', 'published')->integer()->notNull()->default(1)->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'language')
                && $schema->hasColumn('#__citations', 'notes')
            ) {
                $schema->addColumn('#__citations', 'language')->string(100)->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'accession_number')
                && $schema->hasColumn('#__citations', 'language')
            ) {
                $schema->addColumn('#__citations', 'accession_number')->string(100)->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'short_title')
                && $schema->hasColumn('#__citations', 'accession_number')
            ) {
                $schema->addColumn('#__citations', 'short_title')->string(250)->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'author_address')
                && $schema->hasColumn('#__citations', 'short_title')
            ) {
                $schema->addColumn('#__citations', 'author_address')->text()->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'keywords')
                && $schema->hasColumn('#__citations', 'author_address')
            ) {
                $schema->addColumn('#__citations', 'keywords')->text()->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'abstract')
                && $schema->hasColumn('#__citations', 'keywords')
            ) {
                $schema->addColumn('#__citations', 'abstract')->text()->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'call_number')
                && $schema->hasColumn('#__citations', 'abstract')
            ) {
                $schema->addColumn('#__citations', 'call_number')->string(100)->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'label')
                && $schema->hasColumn('#__citations', 'call_number')
            ) {
                $schema->addColumn('#__citations', 'label')->string(100)->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'research_notes')
                && $schema->hasColumn('#__citations', 'label')
            ) {
                $schema->addColumn('#__citations', 'research_notes')->text()->nullable()->execute();
            }

            if (
                !$schema->hasColumn('#__citations', 'params')
                && $schema->hasColumn('#__citations', 'research_notes')
            ) {
                $schema->addColumn('#__citations', 'params')->text()->nullable()->execute();
            }

            $schema->addFulltextIndex('#__citations', 'ftidx_title_isbn_doi_abstract', [
                'title',
                'isbn',
                'doi',
                'abstract',
            ]);
        }

        if ($schema->tableExists('#__citations_assoc')) {
            if (
                $schema->hasColumn('#__citations_assoc', 'table')
                && !$schema->hasColumn('#__citations_assoc', 'tbl')
            ) {
                $schema->renameColumn('#__citations_assoc', 'table', 'tbl')->string(50)->nullable()->execute();
            }
        }

        if ($schema->tableExists('#__citations_authors')) {
            if (
                $schema->hasColumn('#__citations_authors', 'author_uid')
                && !$schema->hasColumn('#__citations_authors', 'authorid')
            ) {
                $schema->renameColumn('#__citations_authors', 'author_uid', 'authorid')
                    ->integer()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if (
                !$schema->hasColumn('#__citations_authors', 'uidNumber')
                && $schema->hasColumn('#__citations_authors', 'authorid')
            ) {
                $schema->addColumn('#__citations_authors', 'uidNumber')
                    ->integer()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            $schema->addUniqueIndex('#__citations_authors', 'uidx_cid_author_authorid_uidNumber', [
                'cid',
                'author',
                'authorid',
                'uidNumber',
            ]);

            $schema->addIndex('#__citations_authors', 'idx_authorid', 'authorid');

            $schema->addIndex('#__citations_authors', 'idx_uidNumber', 'uidNumber');

            $schema->dropIndex('#__citations_authors', 'cid_auth_uid');
        }

        if ($schema->tableExists('#__doi_mapping')) {
            if (
                $schema->hasColumn('#__doi_mapping', 'alias')
                && !$schema->hasColumn('#__doi_mapping', 'versionid')
            ) {
                $schema->addColumn('#__doi_mapping', 'versionid')
                    ->integer()
                    ->nullable()
                    ->default(0)
                    ->execute();
            }

            if (
                $schema->hasColumn('#__doi_mapping', 'versionid')
                && !$schema->hasColumn('#__doi_mapping', 'doi')
            ) {
                $schema->addColumn('#__doi_mapping', 'doi')->string(50)->nullable()->execute();
            }
        }

        if (
            $schema->tableExists('#__events')
            && $schema->hasColumn('#__events', 'publish_down')
            && !$schema->hasColumn('#__events', 'time_zone')
        ) {
            $schema->addColumn('#__events', 'time_zone')->string(5)->nullable()->execute();
        }

        if ($schema->tableExists('#__faq')) {
            if (
                $schema->hasColumn('#__faq', 'fulltext')
                && !$schema->hasColumn('#__faq', 'fulltxt')
            ) {
                $schema->renameColumn('#__faq', 'fulltext', 'fulltxt')->text()->nullable()->execute();
            }

            $schema->dropIndex('#__faq', 'jos_faq_title_introtext_fulltext_ftidx');

            $schema->addFulltextIndex('#__faq', 'jos_faq_title_introtext_fulltext_ftidx', [
                'title',
                'params',
                'fulltxt',
            ]);
            $schema->addFulltextIndex('#__faq', 'ftidx_fulltxt', 'fulltxt');

            $schema->dropIndex('#__faq', 'fulltext');
        }

        if ($schema->tableExists('#__faq_categories')) {
            if ($schema->hasColumn('#__faq_categories', 'description')) {
                $schema->modifyColumn('#__faq_categories', 'description')
                    ->string(255)
                    ->nullable()
                    ->default('')
                    ->execute();
            }

            if (
                !$schema->hasColumn('#__faq_categories', 'asset_id')
                && $schema->hasColumn('#__faq_categories', 'access')
            ) {
                $schema->addColumn('#__faq_categories', 'asset_id')->integer()->notNull()->default(0)->execute();
            }
        }

        if ($schema->tableExists('#__faq_comments')) {
            if ($schema->hasColumn('#__faq_comments', 'entry_id')) {
                $schema->modifyColumn('#__faq_comments', 'entry_id')->integer()->notNull()->default(0)->execute();
            }

            if ($schema->hasColumn('#__faq_comments', 'content')) {
                $schema->modifyColumn('#__faq_comments', 'content')->text()->nullable()->execute();
            }

            if ($schema->hasColumn('#__faq_comments', 'created')) {
                $schema->modifyColumn('#__faq_comments', 'created')
                    ->datetime()
                    ->notNull()
                    ->default('0000-00-00 00:00:00')
                    ->execute();
            }

            if ($schema->hasColumn('#__faq_comments', 'created_by')) {
                $schema->modifyColumn('#__faq_comments', 'created_by')->integer()->notNull()->default(0)->execute();
            }

            if ($schema->hasColumn('#__faq_comments', 'anonymous')) {
                $schema->modifyColumn('#__faq_comments', 'anonymous')->tinyInteger()->notNull()->default(0)->execute();
            }

            if ($schema->hasColumn('#__faq_comments', 'parent')) {
                $schema->modifyColumn('#__faq_comments', 'parent')->integer()->notNull()->default(0)->execute();
            }

            if (
                $schema->hasColumn('#__faq_comments', 'parent')
                && !$schema->hasColumn('#__faq_comments', 'asset_id')
            ) {
                $schema->addColumn('#__faq_comments', 'asset_id')->integer()->notNull()->default(0)->execute();
            }

            if (
                $schema->hasColumn('#__faq_comments', 'asset_id')
                && !$schema->hasColumn('#__faq_comments', 'helpful')
            ) {
                $schema->addColumn('#__faq_comments', 'helpful')->integer()->notNull()->default(0)->execute();
            }

            if (
                $schema->hasColumn('#__faq_comments', 'helpful')
                && !$schema->hasColumn('#__faq_comments', 'nothelpful')
            ) {
                $schema->addColumn('#__faq_comments', 'nothelpful')->integer()->notNull()->default(0)->execute();
            }
        }

        if ($schema->tableExists('#__password_rule')) {
            if (
                $schema->hasColumn('#__password_rule', 'group')
                && !$schema->hasColumn('#__password_rule', 'grp')
                && $schema->hasColumn('#__password_rule', 'failuremsg')
            ) {
                $schema->renameColumn('#__password_rule', 'group', 'grp')->char(32)->notNull()->execute();
            }
        }

        if (!$schema->tableExists('#__polls') && $schema->tableExists('#__xpolls')) {
            $schema->renameTable('#__xpolls', '#__polls');
        }

        if (!$schema->tableExists('#__poll_data') && $schema->tableExists('#__xpoll_data')) {
            $schema->renameTable('#__xpoll_data', '#__poll_data');
        }

        if (!$schema->tableExists('#__poll_date') && $schema->tableExists('#__xpoll_date')) {
            $schema->renameTable('#__xpoll_date', '#__poll_date');
        }

        if (!$schema->tableExists('#__poll_menu') && $schema->tableExists('#__xpoll_menu')) {
            $schema->renameTable('#__xpoll_menu', '#__poll_menu');
        }

        if (
            $schema->tableExists('#__resource_types')
            && $schema->hasColumn('#__resource_types', 'id')
            && !$schema->hasColumn('#__resource_types', 'alias')
        ) {
            $schema->addColumn('#__resource_types', 'alias')->string(100)->nullable()->execute();
        }

        if ($schema->tableExists('#__resources')) {
            if (
                $schema->hasColumn('#__resources', 'fulltext')
                && !$schema->hasColumn('#__resources', 'fulltxt')
                && $schema->hasColumn('#__resources', 'introtext')
            ) {
                $schema->renameColumn('#__resources', 'fulltext', 'fulltxt')->text()->notNull()->execute();
            }

            $schema->dropIndex('#__resources', 'introtext');

            $schema->addFulltextIndex('#__resources', 'ftidx_introtext_fulltxt', ['introtext', 'fulltxt']);

            $schema->dropIndex('#__resources', 'jos_resources_title_introtext_fulltext_ftidx');

            $schema->addFulltextIndex('#__resources', 'ftidx_title_introtext_fulltxt', [
                'title',
                'introtext',
                'fulltxt',
            ]);
        }

        if ($schema->tableExists('#__session')) {
            if ($schema->hasPrimaryKey('#__session')) {
                $schema->table('#__session')->alter()
                    ->dropPrimaryKey()
                    ->addPrimaryKey('session_id')
                    ->execute();
            } elseif ($schema->hasColumn('#__session', 'session_id')) {
                $schema->table('#__session')->alter()
                    ->addPrimaryKey('session_id')
                    ->execute();
            }
        }

        if ($schema->tableExists('#__stats_topvals')) {
            if ($schema->hasColumn('#__stats_topvals', 'rank')) {
                $schema->modifyColumn('#__stats_topvals', 'rank')->smallInteger()->notNull()->default(0)->execute();
            }
        }

        if ($schema->tableExists('#__support_tickets')) {
            if (
                !$schema->hasColumn('#__support_tickets', 'open')
                && $schema->hasColumn('#__support_tickets', 'group')
            ) {
                $schema->addColumn('#__support_tickets', 'open')->tinyInteger()->notNull()->default(1)->execute();
            }
        }

        if ($schema->tableExists('#__tags')) {
            if ($schema->hasColumn('#__tags', 'alias')) {
                $schema->dropColumn('#__tags', 'alias');
            }

            $schema->dropIndex('#__tags', 'jos_tags_raw_tag_alias_description_ftidx');

            $schema->addFulltextIndex('#__tags', 'jos_tags_raw_tag_alias_description_ftidx', [
                'raw_tag',
                'description',
            ]);
        }

        if ($schema->tableExists('#__tags_object')) {
            if (
                !$schema->hasColumn('#__tags_object', 'label')
                && $schema->hasColumn('#__tags_object', 'tbl')
            ) {
                $schema->addColumn('#__tags_object', 'label')->string(30)->nullable()->execute();
            }

            $schema->addIndex('#__tags_object', 'jos_tags_object_objectid_tbl_idx', ['objectid', 'tbl']);
        }

        if ($schema->tableExists('#__tool')) {
            if (
                $schema->hasColumn('#__tool', 'fulltext')
                && $schema->hasColumn('#__tool', 'description')
                && !$schema->hasColumn('#__tool', 'fulltxt')
            ) {
                $schema->renameColumn('#__tool', 'fulltext', 'fulltxt')->text()->nullable()->execute();
            }
        }

        if ($schema->tableExists('#__tool_version')) {
            if (
                $schema->hasColumn('#__tool_version', 'fulltext')
                && $schema->hasColumn('#__tool_version', 'description')
                && !$schema->hasColumn('#__tool_version', 'fulltxt')
            ) {
                $schema->renameColumn('#__tool_version', 'fulltext', 'fulltxt')->text()->nullable()->execute();
            }

            if (
                $schema->hasColumn('#__tool_version', 'priority')
                && !$schema->hasColumn('#__tool_version', 'params')
            ) {
                $schema->addColumn('#__tool_version', 'params')->text()->nullable()->execute();
            }
        }

        if ($schema->tableExists('#__users_password')) {
            if ($schema->hasColumn('#__users_password', 'user_id')) {
                $schema->modifyColumn('#__users_password', 'user_id')->integer()->notNull()->execute();
            }
        }

        if ($schema->tableExists('#__users_password_history')) {
            if (
                $schema->hasColumn('#__users_password_history', 'user_id')
                && $schema->hasColumn('#__users_password_history', 'id')
            ) {
                $schema->modifyColumn('#__users_password_history', 'user_id')->integer()->notNull()->execute();
            }

            if (
                $schema->hasColumn('#__users_password_history', 'passhash')
                && $schema->hasColumn('#__users_password_history', 'user_id')
            ) {
                $schema->modifyColumn('#__users_password_history', 'passhash')->char(32)->notNull()->execute();
            }

            // Add id column if it doesn't exist
            if (!$schema->hasColumn('#__users_password_history', 'id')) {
                $schema->addColumn('#__users_password_history', 'id')->integer()->notNull()->execute();
            }

            // Modify the id column to be auto-increment and set as primary key
            if ($schema->hasColumn('#__users_password_history', 'id')) {
                $pkColumn = $schema->getPrimaryKey('#__users_password_history');
                if ($pkColumn == 'user_id') {
                    // Drop old primary key and add new one with auto-increment
                    $schema->table('#__users_password_history')->alter()
                        ->dropPrimaryKey()
                        ->modifyColumn('id')->integer()->notNull()->autoIncrement()
                        ->addPrimaryKey('id')
                        ->execute();
                } elseif (!$schema->hasPrimaryKey('#__users_password_history')) {
                    // Just add primary key with auto-increment
                    $schema->table('#__users_password_history')->alter()
                        ->modifyColumn('id')->integer()->notNull()->autoIncrement()
                        ->addPrimaryKey('id')
                        ->execute();
                }
            }
        }

        if ($schema->tableExists('#__users_transactions')) {
            $schema->addIndex('#__users_transactions', 'idx_referenceid_category_type', [
                'referenceid',
                'category',
                'type',
            ]);
        }

        if ($schema->tableExists('#__vote_log')) {
            $schema->addIndex('#__vote_log', 'idx_referenceid', 'referenceid');
        }

        if ($schema->tableExists('#__wiki_math')) {
            if ($schema->hasColumn('#__wiki_math', 'inputhash')) {
                $schema->modifyColumn('#__wiki_math', 'inputhash')->string(32)->notNull()->default('')->execute();
            }

            if ($schema->hasColumn('#__wiki_math', 'outputhash')) {
                $schema->modifyColumn('#__wiki_math', 'outputhash')->string(32)->notNull()->default('')->execute();
            }
        }

        if (
            $schema->tableExists('#__wiki_page')
            && !$schema->hasColumn('#__wiki_page', 'group_cn')
            && $schema->hasColumn('#__wiki_page', 'group')
            && $schema->hasColumn('#__wiki_page', 'access')
        ) {
            $schema->renameColumn('#__wiki_page', 'group', 'group_cn')->string(255)->nullable()->execute();
        }

        if ($schema->tableExists('#__wiki_version')) {
            $schema->addIndex('#__wiki_version', 'idx_pageid', 'pageid');
        }

        if ($schema->tableExists('#__wishlist_item')) {
            $schema->addIndex('#__wishlist_item', 'idx_wishlist', 'wishlist');
        }

        if ($schema->tableExists('#__wishlist_vote')) {
            $schema->addIndex('#__wishlist_vote', 'idx_wishid', 'wishid');
        }

        if ($schema->tableExists('#__xgroups')) {
            if (
                $schema->hasColumn('#__xgroups', 'privacy')
                && !$schema->hasColumn('#__xgroups', 'discussion_email_autosubscribe')
            ) {
                $schema->addColumn('#__xgroups', 'discussion_email_autosubscribe')
                    ->tinyInteger()
                    ->nullable()
                    ->execute();
            }

            if (
                $schema->hasColumn('#__xgroups', 'plugins')
                && !$schema->hasColumn('#__xgroups', 'created')
            ) {
                $schema->addColumn('#__xgroups', 'created')->datetime()->nullable()->execute();
            }

            if (
                $schema->hasColumn('#__xgroups', 'created')
                && !$schema->hasColumn('#__xgroups', 'created_by')
            ) {
                $schema->addColumn('#__xgroups', 'created_by')->integer()->nullable()->execute();
            }

            if (
                $schema->hasColumn('#__xgroups', 'created_by')
                && !$schema->hasColumn('#__xgroups', 'params')
            ) {
                $schema->addColumn('#__xgroups', 'params')->text()->nullable()->execute();
            }
        }

        if ($schema->tableExists('#__xgroups_events') && $schema->hasColumn('#__xgroups_events', 'active')) {
            $schema->modifyColumn('#__xgroups_events', 'active')->tinyInteger(1)->notNull()->execute();
        }

        if (
            $schema->tableExists('#__xgroups_pages')
            && $schema->hasColumn('#__xgroups_pages', 'active')
            && !$schema->hasColumn('#__xgroups_pages', 'privacy')
        ) {
            $schema->addColumn('#__xgroups_pages', 'privacy')->string(10)->nullable()->execute();
        }

        if ($schema->tableExists('#__xgroups_tracperm')) {
            $schema->addUniqueIndex('#__xgroups_tracperm', 'id', ['group_id', 'action']);

            $schema->dropPrimaryKey('#__xgroups_tracperm');
        }

        if (
            $schema->tableExists('#__xprofiles')
            && $schema->hasColumn('#__xprofiles', 'shadowExpire')
            && !$schema->hasColumn('#__xprofiles', 'locked')
        ) {
            $schema->addColumn('#__xprofiles', 'locked')->tinyInteger()->notNull()->default(0)->execute();
        }

        if ($schema->tableExists('#__ysearch_site_map')) {
            $schema->addFulltextIndex('#__ysearch_site_map', 'ftidx_title_description', ['title', 'description']);

            $schema->dropIndex('#__ysearch_site_map', 'ysearch_site_map_title_description_ftidx');

            $schema->dropIndex('#__ysearch_site_map', 'jos_ysearch_site_map_title_description_ftidx');
        }
    }
}
