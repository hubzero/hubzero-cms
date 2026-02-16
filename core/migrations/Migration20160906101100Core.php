<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to fix index naming conventions in CNS tables
 *
*/
class Migration20160906101100Core extends Base
{
    public function up()
    {
        $schema = $this->db->schema();

        $schema->addIndex('#__xsession', 'idx_ip', 'ip');
        $schema->dropIndex('#__xsession', 'ip');
        $schema->addFulltextIndex('#__xprofiles_bio', 'ftidx_bio', 'bio');
        $schema->dropIndex('#__xprofiles_bio', 'jos_xprofiles_bio_bio_ftidx');
        $schema->addIndex('#__xprofiles', 'idx_username', 'username');
        $schema->dropIndex('#__xprofiles', 'username');
        $schema->addFulltextIndex('#__xprofiles', 'ftidx_givenName_surname', array('givenName', 'surname'));
        $schema->dropIndex('#__xprofiles', 'author');
        $schema->addFulltextIndex('#__xprofiles', 'ftidx_name', 'name');
        $schema->dropIndex('#__xprofiles', 'jos_xprofiles_name_ftidx');
        $schema->addPrimaryKey("#__xmessage_action", 'id');
        $schema->dropIndex('#__xmessage_action', 'id');
        $schema->addIndex('#__xmessage_action', 'idx_class', 'class');
        $schema->dropIndex('#__xmessage_action', 'class');
        $schema->addIndex('#__xmessage_action', 'idx_element', 'element');
        $schema->dropIndex('#__xmessage_action', 'element');
        $schema->addFulltextIndex(
            '#__xgroups',
            'ftidx_cn_description_public_desc',
            array('cn', 'description', 'public_desc')
        );
        $schema->dropIndex('#__xgroups', 'jos_xgroups_cn_description_public_desc_ftidx');
        $schema->addFulltextIndex('#__wishlist_item', 'ftidx_subject_about', array('subject', 'about'));
        $schema->dropIndex('#__wishlist_item', 'jos_wishlist_item_subject_about_ftidx');
        $schema->dropIndex('#__wishlist_item', 'jos_wishlist_item_wishlist_idx');
        $schema->addFulltextIndex('#__wishlist_implementation', 'ftidx_pagetext', 'pagetext');
        $schema->dropIndex('#__wishlist_implementation', 'pagetext');
        $schema->addFulltextIndex('#__wiki_versions', 'ftidx_pagetext', 'pagetext');
        $schema->dropIndex('#__wiki_versions', 'pagetext');
        $schema->addFulltextIndex('#__wiki_pages', 'ftidx_title', 'title');
        $schema->dropIndex('#__wiki_pages', 'title');
        $schema->addIndex('#__wiki_links', 'idx_scope_scope_id', array('scope', 'scope_id'));
        $schema->dropIndex('#__wiki_links', 'idx_scoped');
        $schema->addUniqueIndex('#__wiki_formulas', 'uidx_inputhash', 'inputhash');
        $schema->dropIndex('#__wiki_formulas', 'inputhash');
        $schema->dropIndex('#__vote_log', 'jos_vote_log_referenceid_idx');
        $schema->addUniqueIndex('#__users_points_services', 'uidx_alias', 'alias');
        $schema->dropIndex('#__users_points_services', 'alias');
        $schema->addUniqueIndex('#__user_roles', 'uidx_role_user_id_group_id', ['role', 'user_id', 'group_id']);
        $schema->dropIndex('#__user_roles', 'jos_user_roles_role_user_id_group_id_uidx');
        $schema->addUniqueIndex('#__trac_user_permission', 'uidx_user_id_action_trac_project_id', [
            'user_id',
            'action',
            'trac_project_id',
        ]);
        $schema->dropIndex('#__trac_user_permission', 'trac_action');
        $schema->addUniqueIndex('#__trac_group_permission', 'uidx_group_id_action_trac_project_id', [
            'group_id',
            'action',
            'trac_project_id',
        ]);
        $schema->dropIndex('#__trac_group_permission', 'trac_action');
        $schema->addUniqueIndex('#__tool_version_tracperm', 'uidx_tool_version_id_tracperm', [
            'tool_version_id',
            'tracperm',
        ]);
        $schema->dropIndex('#__tool_version_tracperm', 'toolid');
        $schema->addUniqueIndex('#__tool_version_middleware', 'uidx_tool_version_id_middleware', [
            'tool_version_id',
            'middleware',
        ]);
        $schema->dropIndex('#__tool_version_middleware', 'toolid');
        $schema->addUniqueIndex('#__tool_version_hostreq', 'uidx_tool_version_id_hostreq', [
            'tool_version_id',
            'hostreq',
        ]);
        $schema->dropIndex('#__tool_version_hostreq', 'toolid');
        $schema->dropIndex('#__tool_version_hostreq', 'idx_tool_version_id_hostreq');
        $schema->addUniqueIndex('#__tool_version', 'uidx_toolname_instance', ['toolname', 'instance']);
        $schema->dropIndex('#__tool_version', 'toolname');
        $schema->addIndex('#__tool_version', 'idx_instance', 'instance');
        $schema->dropIndex('#__tool_version', 'instance');
        $schema->addUniqueIndex('#__tool', 'uidx_toolname', 'toolname');
        $schema->dropIndex('#__tool', 'toolname');
        $schema->addIndex('#__stats_topvals', 'idx_top', 'top');
        $schema->dropIndex('#__stats_topvals', 'top');
        $schema->addIndex('#__stats_topvals', 'idx_top_rank', array('top', 'rank'));
        $schema->dropIndex('#__stats_topvals', 'top_2');
        $schema->addIndex('#__stats_topvals', 'idx_top_datetime', array('top', 'datetime'));
        $schema->dropIndex('#__stats_topvals', 'top_3');
        $schema->addIndex('#__stats_topvals', 'idx_top_datetime_rank', array('top', 'datetime', 'rank'));
        $schema->dropIndex('#__stats_topvals', 'top_4');
        $schema->addIndex('#__stats_topvals', 'idx_top_datetime_period', array('top', 'datetime', 'period'));
        $schema->dropIndex('#__stats_topvals', 'top_5');
        $schema->addIndex('#__session_geo', 'idx_userid', 'userid');
        $schema->dropIndex('#__session_geo', 'userid');
        $schema->addIndex('#__session_geo', 'idx_time', 'time');
        $schema->dropIndex('#__session_geo', 'time');
        $schema->addIndex('#__session_geo', 'idx_ip', 'ip');
        $schema->dropIndex('#__session_geo', 'ip');
        $schema->addFulltextIndex('#__resources', 'ftidx_title', 'title');
        $schema->dropIndex('#__resources', 'title');
        $schema->addPrimaryKey('#__resource_stats_tools_users', 'id');
        $schema->dropIndex('#__resource_stats_tools_users', 'id');
        $schema->addPrimaryKey('#__resource_stats_tools', 'id');
        $schema->dropIndex('#__resource_stats_tools', 'id');
        $schema->addIndex('#__resource_stats_clusters', 'idx_cluster', 'cluster');
        $schema->dropIndex('#__resource_stats_clusters', 'cluster');
        $schema->addIndex('#__resource_stats_clusters', 'idx_username', 'username');
        $schema->dropIndex('#__resource_stats_clusters', 'username');
        $schema->addIndex('#__resource_stats_clusters', 'idx_uidNumber', 'uidNumber');
        $schema->dropIndex('#__resource_stats_clusters', 'uidNumber');
        $schema->addIndex('#__resource_stats_clusters', 'idx_toolname', 'toolname');
        $schema->dropIndex('#__resource_stats_clusters', 'toolname');
        $schema->addIndex('#__resource_stats_clusters', 'idx_resid', 'resid');
        $schema->dropIndex('#__resource_stats_clusters', 'resid');
        $schema->addIndex('#__resource_stats_clusters', 'idx_clustersize', 'clustersize');
        $schema->dropIndex('#__resource_stats_clusters', 'clustersize');
        $schema->addIndex('#__resource_stats_clusters', 'idx_cluster_start', 'cluster_start');
        $schema->dropIndex('#__resource_stats_clusters', 'cluster_start');
        $schema->addIndex('#__resource_stats_clusters', 'idx_cluster_end', 'cluster_end');
        $schema->dropIndex('#__resource_stats_clusters', 'cluster_end');
        $schema->addIndex('#__resource_stats_clusters', 'idx_institution', 'institution');
        $schema->dropIndex('#__resource_stats_clusters', 'institution');
        $schema->addUniqueIndex('#__resource_stats', 'uidx_resid_restype_datetime_period', [
            'resid',
            'restype',
            'datetime',
            'period',
        ]);
        $schema->dropIndex('#__resource_stats', 'res_stats');
        $schema->addPrimaryKey('#__publication_stats', 'id');
        $schema->dropIndex('#__publication_stats', 'id');
        $schema->addUniqueIndex('#__publication_stats', 'uidx_publication_id_datetime_period', [
            'publication_id',
            'datetime',
            'period',
        ]);
        $schema->dropIndex('#__publication_stats', 'pub_stats');
        $schema->addUniqueIndex('#__publication_master_types', 'uidx_alias', 'alias');
        $schema->dropIndex('#__publication_master_types', 'alias');
        $schema->addUniqueIndex('#__publication_categories', 'uidx_name', 'name');
        $schema->dropIndex('#__publication_categories', 'type');
        $schema->dropIndex('#__publication_categories', 'name');
        $schema->addUniqueIndex('#__publication_categories', 'uidx_alias', 'alias');
        $schema->dropIndex('#__publication_categories', 'alias');
        $schema->addUniqueIndex('#__publication_categories', 'uidx_url_alias', 'url_alias');
        $schema->dropIndex('#__publication_categories', 'url_alias');
        $schema->addUniqueIndex('#__projects', 'uidx_alias', 'alias');
        $schema->dropIndex('#__projects', 'alias');
        $schema->addUniqueIndex('#__project_public_stamps', 'uidx_stamp', 'stamp');
        $schema->dropIndex('#__project_public_stamps', 'stamp');
        $schema->addFulltextIndex('#__project_microblog', 'ftidx_blogentry', 'blogentry');
        $schema->dropIndex('#__project_microblog', 'title');
        $schema->addIndex('#__project_logs', 'idx_projectid', 'projectid');
        $schema->dropIndex('#__project_logs', 'projectid');

        $schema->addIndex('#__poll_options', 'idx_pollid_text', ['poll_id', 'text']);

        $schema->dropIndex('#__poll_options', 'pollid');
        $schema->addIndex('#__poll_dates', 'idx_poll_id', 'poll_id');
        $schema->dropIndex('#__poll_dates', 'poll_id');
        $schema->addUniqueIndex('#__oauthp_nonces', 'uidx_nonce_stamp', ['nonce', 'stamp']);
        $schema->dropIndex('#__oauthp_nonces', 'unonce');
        $schema->addIndex('#__metrics_ipgeo_cache', 'idx_lookup_datetime', 'lookup_datetime');
        $schema->dropIndex('#__metrics_ipgeo_cache', 'lookup_datetime');
        $schema->addFulltextIndex('#__kb_articles', 'ftidx_title', 'title');
        $schema->dropIndex('#__kb_articles', 'title');
        $schema->addFulltextIndex('#__kb_articles', 'ftidx_title_params_fulltxt', array('title', 'params', 'fulltxt'));
        $schema->dropIndex('#__kb_articles', 'introtext');
        $schema->addFulltextIndex('#__kb_articles', 'ftidx_params', 'params');
        $schema->dropIndex('#__kb_articles', 'fulltxt');
        $schema->addPrimaryKey('#__item_comment_files', 'id');
        $schema->dropIndex('#__item_comment_files', 'id');
        $schema->addPrimaryKey('#__incremental_registration_labels', 'id');
        $schema->dropIndex('#__incremental_registration_labels', 'id');
        $schema->addPrimaryKey('#__incremental_registration_groups', 'id');
        $schema->dropIndex('#__incremental_registration_groups', 'id');
        $schema->addPrimaryKey('#__incremental_registration_group_label_rel', 'id');
        $schema->dropIndex('#__incremental_registration_group_label_rel', 'id');
        $schema->addFulltextIndex('#__forum_posts', 'ftidx_comment_title', array('comment', 'title'));
        $schema->dropIndex('#__forum_posts', 'comment_title_fidx');
        $schema->addFulltextIndex('#__forum_posts', 'ftidx_comment', 'comment');
        $schema->dropIndex('#__forum_posts', 'question');
        $schema->addIndex('#__forum_posts', 'idx_scope_scope_id', array('scope', 'scope_id'));
        $schema->addIndex('#__forum_categories', 'idx_scope_scope_id', array('scope', 'scope_id'));
        $schema->addIndex('#__forum_attachments', 'idx_filename_post_id', array('filename', 'post_id'));
        $schema->addPrimaryKey('#__focus_areas', 'id');
        $schema->dropIndex('#__focus_areas', 'id');
        $schema->addPrimaryKey('#__focus_area_resource_type_rel', 'id');
        $schema->dropIndex('#__focus_area_resource_type_rel', 'id');
        $schema->addFulltextIndex('#__events', 'ftidx_title', 'title');
        $schema->dropIndex('#__events', 'title');
        $schema->addFulltextIndex('#__events', 'ftidx_content', 'content');
        $schema->dropIndex('#__events', 'content');
        $schema->addFulltextIndex('#__events', 'ftidx_title_content', array('title', 'content'));
        $schema->dropIndex('#__events', 'jos_events_title_content_ftidx');
        $schema->addUniqueIndex('#__document_text_data', 'uidx_hash', 'hash');
        $schema->dropIndex('#__document_text_data', 'jos_document_text_data_hash_uidx');
        $schema->addFulltextIndex('#__document_text_data', 'ftidx_body', 'body');
        $schema->dropIndex('#__document_text_data', 'jos_document_text_data_body_ftidx');
        $schema->addUniqueIndex('#__document_resource_rel', 'uidx_id', 'id');
        $schema->dropIndex('#__document_resource_rel', 'id');
        $schema->addUniqueIndex('#__document_resource_rel', 'uidx_document_id_resource_id', [
            'document_id',
            'resource_id',
        ]);
        $schema->dropIndex(
            '#__document_resource_rel',
            'jos_document_resource_rel_document_id_resource_id_uidx'
        );
        $schema->addUniqueIndex('#__courses_member_badges', 'uidx_member_id', 'member_id');
        $schema->dropIndex('#__courses_member_badges', 'member_id');
        $schema->addUniqueIndex('#__courses_grade_book', 'uidx_user_id_scope_scope_id', [
            'member_id',
            'scope',
            'scope_id',
        ]);
        $schema->addPrimaryKey('#__courses_forms', 'id');
        $schema->dropIndex('#__courses_forms', 'id');
        $schema->addPrimaryKey('#__courses_form_responses', 'id');
        $schema->dropIndex('#__courses_form_responses', 'id');
        $schema->dropIndex('#__courses_form_respondents', 'jos_pdf_form_responses_respondent_id_idx');
        $schema->addPrimaryKey('#__courses_form_respondents', 'id');
        $schema->dropIndex('#__courses_form_respondents', 'id');
        $schema->addUniqueIndex('#__courses_form_respondent_progress', 'uidx_respondent_id_question_id', [
            'respondent_id',
            'question_id',
        ]);
        $schema->addPrimaryKey('#__courses_form_respondent_progress', 'id');
        $schema->dropIndex('#__courses_form_respondent_progress', 'id');
        $schema->dropIndex(
            '#__courses_form_respondent_progress',
            'jos_pdf_form_respondent_progress_respondent_id_question_id_uidx'
        );
        $schema->addPrimaryKey('#__courses_form_questions', 'id');
        $schema->dropIndex('#__courses_form_deployments', 'id');
        $schema->addPrimaryKey('#__courses_form_deployments', 'id');
        $schema->dropIndex('#__courses_form_answers', 'id');
        $schema->addPrimaryKey('#__courses_form_answers', 'id');
        $schema->dropIndex('#__courses_form_questions', 'id');
        $schema->addFulltextIndex(
            '#__content',
            'ftidx_title_introtext_fulltext',
            array('title', 'introtext', 'fulltext')
        );
        $schema->addFulltextIndex('#__content', 'ftidx_introtext_fulltext', array('introtext', 'fulltext'));
        $schema->addFulltextIndex('#__content', 'ftidx_title', 'title');
        $schema->dropIndex('#__content', 'jos_content_state_idx');
        $schema->dropIndex('#__content', 'title');
        $schema->dropIndex('#__content', 'introtext');
        $schema->dropIndex('#__content', 'jos_content_title_introtext_fulltext_ftidx');
        $schema->addIndex('#__collections_votes', 'idx_item_id_user_id', array('item_id', 'user_id'));
        $schema->dropIndex('#__collections_votes', 'idx_item_user');
        $schema->addIndex('#__collections', 'idx_object_type_object_id', array('object_type', 'object_id'));
        $schema->dropIndex('#__collections', 'idx_objectified');
        $schema->addIndex('#__collections', 'idx_created_by', 'created_by');
        $schema->dropIndex('#__collections', 'idx_createdby');
        $schema->dropIndex('#__citations_authors', 'cid_auth_authid_uid');
        $schema->dropIndex('#__citations_authors', 'authorid');
        $schema->dropIndex('#__citations_authors', 'uidNumber');
        $schema->dropIndex('#__citations', 'jos_citations_title_isbn_doi_abstract_ftidx');

        $schema->addUniqueIndex('#__cart_saved_addresses', 'uidx_uidNumber_saToFirst_saToLast_saAddress_saZip', [
            'uidNumber',
            'saToFirst',
            'saToLast',
            'saAddress',
            'saZip',
        ]);

        $schema->dropIndex('#__cart_saved_addresses', 'uidNumber');
        $schema->addUniqueIndex('#__cart_memberships', 'uidx_pId_crtId', ['pId', 'crtId']);
        $schema->dropIndex('#__cart_memberships', 'pId');
        $schema->addUniqueIndex('#__cart_carts', 'uidx_uidNumber', 'uidNumber');
        $schema->dropIndex('#__cart_carts', 'uidNumber');
        $schema->addFulltextIndex('#__blog_entries', 'ftidx_title', 'title');
        $schema->dropIndex('#__blog_entries', 'title');
        $schema->addFulltextIndex('#__blog_entries', 'ftidx_content', 'content');
        $schema->dropIndex('#__blog_entries', 'content');
        $schema->addFulltextIndex('#__answers_responses', 'ftidx_answer', 'answer');
        $schema->dropIndex('#__answers_responses', 'answer');
        $schema->addFulltextIndex('#__answers_questions', 'ftidx_question', 'question');
        $schema->dropIndex('#__answers_questions', 'question');
        $schema->addFulltextIndex('#__answers_questions', 'ftidx_subject', 'subject');
        $schema->dropIndex('#__answers_questions', 'subject');
    }

    public function down()
    {
    }
}
