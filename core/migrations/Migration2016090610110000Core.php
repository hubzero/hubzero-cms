<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to fix index naming conventions in CNS tables
 **/
class Migration2016090610110000Core extends Base
{
	private function addIndex($table,$key,$fields)
	{
		if (is_array($fields))
		{
			$indexfields = '';

			foreach ($fields as $f)
			{
				$indexfields .= "`" . $f . "`,";
			}
		}
		else
		{
			$indexfields = "`" . $fields . "`";
		}

		$indexfields = trim($indexfields,",");

                if (!$this->db->tableHasKey($table, $key))
                {
                        $query = "ALTER TABLE `" . $table . "` ADD INDEX `" . $key . "` (" . $indexfields . ");";
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	private function addPrimaryIndex($table,$fields)
	{
		if (is_array($fields))
		{
			$indexfields = '';

			foreach ($fields as $f)
			{
				$indexfields .= "`" . $f . "`,";
			}
		}
		else
		{
			$indexfields = "`" . $fields . "`";
		}

		$indexfields = trim($indexfields,",");

                if (!$this->db->tableHasKey($table, 'PRIMARY'))
                {
                        $query = "ALTER TABLE `" . $table . "` ADD PRIMARY KEY (" . $indexfields . ");";
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	private function addFulltextIndex($table,$key,$fields)
	{
		if (is_array($fields))
		{
			$indexfields = '';

			foreach ($fields as $f)
			{
				$indexfields .= "`" . $f . "`,";
			}
		}
		else
		{
			$indexfields = "`" . $fields . "`";
		}

		$indexfields = trim($indexfields,",");

                if (!$this->db->tableHasKey($table, $key))
                {
                        $query = "ALTER TABLE `" . $table . "` ADD FULLTEXT INDEX `" . $key . "` (" . $indexfields . ");";
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	private function addUniqueIndex($table,$key,$fields,$using='')
	{
		if (is_array($fields))
		{
			$indexfields = '';

			foreach ($fields as $f)
			{
				$indexfields .= "`" . $f . "`,";
			}
		}
		else
		{
			$indexfields = "`" . $fields . "`";
		}

		$indexfields = trim($indexfields,",");

                if (!$this->db->tableHasKey($table, $key))
                {
                        $query = "ALTER TABLE `" . $table . "` ADD UNIQUE INDEX `" . $key . "` (" . $indexfields . ")";
			if (!empty($using))
			{
				$query .= " USING $using";
			}
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	private function dropIndex($table,$key)
	{
                if ($this->db->tableHasKey($table,$key))
                {
                        $query = "DROP INDEX `" . $key . "` ON `" . $table . "`;";
			$this->db->setQuery($query);
			$this->db->query();
                }
	}

	public function up()
	{
		$this->addIndex('#__xsession', 'idx_ip', 'ip');
		$this->dropIndex('#__xsession', 'ip');
		$this->addFulltextIndex('#__xprofiles_bio','ftidx_bio','bio');
		$this->dropIndex('#__xprofiles_bio','jos_xprofiles_bio_bio_ftidx');
		$this->addIndex('#__xprofiles','idx_username','username');
		$this->dropIndex('#__xprofiles','username');
		$this->addFulltextIndex('#__xprofiles', 'ftidx_givenName_surname', array('givenName','surname'));
		$this->dropIndex('#__xprofiles','author');
		$this->addFulltextIndex('#__xprofiles', 'ftidx_name','name');
		$this->dropIndex('#__xprofiles','jos_xprofiles_name_ftidx');
		$this->addPrimaryIndex("#__xmessage_action",'id');
		$this->dropIndex('#__xmessage_action','id');
		$this->addIndex('#__xmessage_action','idx_class','class');
		$this->dropIndex('#__xmessage_action','class');
		$this->addIndex('#__xmessage_action','idx_element','element');
		$this->dropIndex('#__xmessage_action','element');
		$this->addFulltextIndex('#__xgroups','ftidx_cn_description_public_desc',array('cn','description','public_desc'));
		$this->dropIndex('#__xgroups','jos_xgroups_cn_description_public_desc_ftidx');
		$this->addFulltextIndex('#__wishlist_item','ftidx_subject_about',array('subject','about'));
		$this->dropIndex('#__wishlist_item','jos_wishlist_item_subject_about_ftidx');
		$this->dropIndex('#__wishlist_item','jos_wishlist_item_wishlist_idx');
		$this->addFulltextIndex('#__wishlist_implementation','ftidx_pagetext','pagetext');
		$this->dropIndex('#__wishlist_item','pagetext');
		$this->addFulltextIndex('#__wiki_versions','ftidx_pagetext','pagetext');
		$this->dropIndex('#__wiki_versions','pagetext');
		$this->addFulltextIndex('#__wiki_pages','ftidx_title','title');
		$this->dropIndex('#__wiki_pages','title');
		$this->addIndex('#__wiki_links','idx_scope_scope_id',array('scope','scope_id'));
		$this->dropIndex('#__wiki_links','idx_scoped');
		$this->addUniqueIndex('#__wiki_formulas','uidx_inputhash','inputhash');
		$this->dropIndex('#__wiki_formulas','inputhash');
		$this->dropIndex('#__vote_log','jos_vote_log_referenceid_idx');
		$this->addUniqueIndex('#__users_points_services','uidx_alias','alias');
		$this->dropIndex('#__users_points_services','alias');
		$this->addUniqueIndex('#__user_roles','uidx_role_user_id_group_id',array('role','user_id','group_id'));
		$this->dropIndex('#__user_roles','jos_user_roles_role_user_id_group_id_uidx');
		$this->addUniqueIndex('#__trac_user_permission', 'uidx_user_id_action_trac_project_id',array('user_id','action','trac_project_id'),'BTREE');
		$this->dropIndex('#__trac_user_permission', 'trac_action');
		$this->addUniqueIndex('#__trac_group_permission','uidx_group_id_action_trac_project_id',array('group_id','action','trac_project_id'),'BTREE');
		$this->dropIndex('#__trac_group_permission','trac_action');
		$this->addUniqueIndex('#__tool_version_tracperm','uidx_tool_version_id_tracperm',array('tool_version_id','tracperm'));
		$this->dropIndex('#__tool_version_tracperm','toolid');
		$this->addUniqueIndex('#__tool_version_middleware','uidx_tool_version_id_middleware',array('tool_version_id','middleware'));
		$this->dropIndex('#__tool_version_middleware','toolid');
		$this->addUniqueIndex('#__tool_version_hostreq','uidx_tool_version_id_hostreq',array('tool_version_id','hostreq'));
		$this->dropIndex('#__tool_version_hostreq','toolid');
		$this->addUniqueIndex('#__tool_version','uidx_toolname_instance',array('toolname','instance'));
		$this->dropIndex('#__tool_version','toolname');
		$this->addIndex('#__tool_version','idx_instance','instance');
		$this->dropIndex('#__tool_version','instance');
		$this->addUniqueIndex('#__tool','uidx_toolname','toolname');
		$this->dropIndex('#__tool','toolname');
		$this->addIndex('#__stats_topvals', 'idx_top', 'top');
		$this->dropIndex('#__stats_topvals', 'top');
		$this->addIndex('#__stats_topvals', 'idx_top_rank', array('top','rank'));
		$this->dropIndex('#__stats_topvals', 'top_2');
		$this->addIndex('#__stats_topvals', 'idx_top_datetime', array('top','datetime'));
		$this->dropIndex('#__stats_topvals', 'top_3');
		$this->addIndex('#__stats_topvals', 'idx_top_datetime_rank', array('top','datetime','rank'));
		$this->dropIndex('#__stats_topvals', 'top_4');
		$this->addIndex('#__stats_topvals', 'idx_top_datetime_period', array('top','datetime','period'));
		$this->dropIndex('#__stats_topvals', 'top_5');
		$this->addIndex('#__session_geo', 'idx_userid', 'userid');
		$this->dropIndex('#__session_geo','userid');
		$this->addIndex('#__session_geo', 'idx_time', 'time');
		$this->dropIndex('#__session_geo', 'time');
		$this->addIndex('#__session_geo', 'idx_ip', 'ip');
		$this->dropIndex('#__session_geo','ip');
		$this->addFulltextIndex('#__resources', 'ftidx_title', 'title');
		$this->dropIndex('#__resources', 'title');
		$this->addPrimaryIndex('#__resource_stats_tools_users', 'id');
		$this->dropIndex('#__resource_stats_tools_users', 'id');
		$this->addPrimaryIndex('#__resource_stats_tools', 'id');
		$this->dropIndex('#__resource_stats_tools', 'id');
		$this->addIndex('#__resource_stats_clusters', 'idx_cluster', 'cluster');
		$this->dropIndex('#__resource_stats_clusters', 'cluster');
		$this->addIndex('#__resource_stats_clusters', 'idx_username', 'username');
		$this->dropIndex('#__resource_stats_clusters', 'username');
		$this->addIndex('#__resource_stats_clusters', 'idx_uidNumber', 'uidNumber');
		$this->dropIndex('#__resource_stats_clusters', 'uidNumber');
		$this->addIndex('#__resource_stats_clusters', 'idx_toolname', 'toolname');
		$this->dropIndex('#__resource_stats_clusters', 'toolname');
		$this->addIndex('#__resource_stats_clusters', 'idx_resid', 'resid');
		$this->dropIndex('#__resource_stats_clusters', 'resid');
		$this->addIndex('#__resource_stats_clusters', 'idx_clustersize', 'clustersize');
		$this->dropIndex('#__resource_stats_clusters', 'clustersize');
		$this->addIndex('#__resource_stats_clusters', 'idx_cluster_start', 'cluster_start');
		$this->dropIndex('#__resource_stats_clusters', 'cluster_start');
		$this->addIndex('#__resource_stats_clusters', 'idx_cluster_end', 'cluster_end');
		$this->dropIndex('#__resource_stats_clusters', 'cluster_end');
		$this->addIndex('#__resource_stats_clusters', 'idx_institution', 'institution');
		$this->dropIndex('#__resource_stats_clusters', 'institution');
		$this->addUniqueIndex('#__resource_stats','uidx_resid_restype_datetime_period', array('resid','restype','datetime','period'));
		$this->dropIndex('#__resource_stats', 'res_stats');
		$this->addPrimaryIndex('#__publication_stats','id');
		$this->dropIndex('#__publication_stats','id');
		$this->addUniqueIndex('#__publication_stats', 'uidx_publication_id_datetime_period', array('publication_id','datetime','period'));
		$this->dropIndex('#__publication_stats','pub_stats');
		$this->addUniqueIndex('#__publication_master_types', 'uidx_alias', 'alias');
		$this->dropIndex('#__publication_master_types', 'alias');
		$this->addUniqueIndex('#__publication_categories', 'uidx_name', 'name');
		$this->dropIndex('#__publication_categories', 'name');
		$this->addUniqueIndex('#__publication_categories', 'uidx_alias', 'alias');
		$this->dropIndex('#__publication_categories', 'alias');
		$this->addUniqueIndex('#__publication_categories', 'uidx_url_alias', 'url_alias');
		$this->dropIndex('#__publication_categories', 'url_alias');
		$this->addUniqueIndex('#__projects', 'uidx_alias', 'alias');
		$this->dropIndex('#__projects', 'alias');
		$this->addUniqueIndex('#__project_public_stamps', 'uidx_stamp', 'stamp');
		$this->dropIndex('#__project_public_stamps', 'stamp');
		$this->addFulltextIndex('#__project_microblog', 'ftidx_blogentry', 'blogentry');
		$this->dropIndex('#__project_microblog', 'title');
		$this->addIndex('#__project_logs', 'idx_projectid', 'projectid');
		$this->dropIndex('#__project_logs', 'projectid');

		if (!$this->db->tableHasKey('#__poll_options', 'idx_pollid_text'))
                {
                        $query = "ALTER TABLE #__poll_options ADD INDEX `idx_pollid_text` (`poll_id`, `text`(1))";
                        $this->db->setQuery($query);
                        $this->db->query();
                }

		$this->dropIndex('#__poll_options', 'pollid');
		$this->addIndex('#__poll_dates', 'idx_poll_id', 'poll_id');
		$this->dropIndex('#__poll_dates', 'poll_id');
		$this->addUniqueIndex('#__oauthp_nonces', 'uidx_nonce_stamp', array('nonce','stamp'));
		$this->dropIndex('#__oauthp_nonces', 'unonce');
		$this->addIndex('#__metrics_ipgeo_cache', 'idx_lookup_datetime', 'lookup_datetime');
		$this->dropIndex('#__metrics_ipgeo_cache', 'lookup_datetime');
		$this->addFulltextIndex('#__kb_articles', 'ftidx_title', 'title');
		$this->dropIndex('#__kb_articles', 'title');
		$this->addFulltextIndex('#__kb_articles', 'ftidx_title_params_fulltxt', array('title','params','fulltxt'));
		$this->dropIndex('#__kb_articles', 'introtext');
		$this->addFulltextIndex('#__kb_articles', 'ftidx_params', 'params');
		$this->dropIndex('#__kb_articles', 'fulltxt');
		$this->addPrimaryIndex('#__item_comment_files', 'id');
		$this->dropIndex('#__item_comment_files', 'id');
		$this->addPrimaryIndex('#__incremental_registration_labels', 'id');
		$this->dropIndex('#__incremental_registration_labels', 'id');
		$this->addPrimaryIndex('#__incremental_registration_groups', 'id');
		$this->dropIndex('#__incremental_registration_groups', 'id');
		$this->addPrimaryIndex('#__incremental_registration_group_label_rel', 'id');
		$this->dropIndex('#__incremental_registration_group_label_rel', 'id');
		$this->addFulltextIndex('#__forum_posts', 'ftidx_comment_title', array('comment','title'));
		$this->dropIndex('#__forum_posts','comment_title_fidx');
		$this->addFulltextIndex('#__forum_posts', 'ftidx_comment', 'comment');
		$this->dropIndex('#__forum_posts', 'question');
		$this->addIndex('#__forum_posts','idx_scope_scope_id',array('scope','scope_id'));
		$this->addIndex('#__forum_categories', 'idx_scope_scope_id', array('scope','scope_id'));
		$this->addIndex('#__forum_attachments', 'idx_filename_post_id', array('filename','post_id'));
		$this->addPrimaryIndex('#__focus_areas', 'id');
		$this->dropIndex('#__focus_areas', 'id');
		$this->addPrimaryIndex('#__focus_area_resource_type_rel', 'id');
		$this->dropIndex('#__focus_area_resource_type_rel', 'id');
		$this->addFulltextIndex('#__events', 'ftidx_title', 'title');
		$this->dropIndex('#__events', 'title');
		$this->addFulltextIndex('#__events', 'ftidx_content', 'content');
		$this->dropIndex('#__events', 'content');
		$this->addFulltextIndex('#__events', 'ftidx_title_content', array('title','content'));
		$this->dropIndex('#__events', 'jos_events_title_content_ftidx');
		$this->addUniqueIndex('#__document_text_data', 'uidx_hash', 'hash');
		$this->dropIndex('#__document_text_data','jos_document_text_data_hash_uidx');
		$this->addFulltextIndex('#__document_text_data','ftidx_body','body');
		$this->dropIndex('#__document_text_data', 'jos_document_text_data_body_ftidx');
		$this->addUniqueIndex('#__document_resource_rel','uidx_id','id');
		$this->dropIndex('#__document_resource_rel', 'id');
		$this->addUniqueIndex('#__document_resource_rel', 'uidx_document_id_resource_id', array('document_id','resource_id'));
		$this->dropIndex('#__document_resource_rel', 'jos_document_resource_rel_document_id_resource_id_uidx');
		$this->addUniqueIndex('#__courses_member_badges','uidx_member_id','member_id');
		$this->dropIndex('#__courses_member_badges','member_id');
		$this->addUniqueIndex('#__courses_grade_book', 'uidx_user_id_scope_scope_id', array('member_id','scope','scope_id'));
		$this->addPrimaryIndex('#__courses_forms','id');
		$this->dropIndex('#__courses_forms','id');
		$this->addPrimaryIndex('#__courses_form_responses','id');
		$this->dropIndex('#__courses_form_responses','id');
		$this->dropIndex('#__courses_form_responses','jos_pdf_form_responses_respondent_id_idx');
		$this->addPrimaryIndex('#__courses_form_respondents','id');
		$this->dropIndex('#__courses_form_respondents','id');
		$this->addUniqueIndex('#__courses_form_respondent_progress', 'uidx_respondent_id_question_id', array('respondent_id','question_id'));
		$this->addPrimaryIndex('#__courses_form_respondent_progress', 'id');
		$this->dropIndex('#__courses_form_respondent_progress', 'id');
		$this->dropIndex('#__courses_form_respondent_progress', 'jos_pdf_form_respondent_progress_respondent_id_question_id_uidx');
		$this->addPrimaryIndex('#__courses_form_questions','id');
		$this->dropIndex('#__courses_form_deployments','id');
		$this->addPrimaryIndex('#__courses_form_deployments','id');
		$this->dropIndex('#__courses_form_answers','id');
		$this->addPrimaryIndex('#__courses_form_answers','id');
		$this->dropIndex('#__courses_form_questions','id');
		$this->addFulltextIndex('#__content', 'ftidx_title_introtext_fulltext', array('title','introtext','fulltext'));
		$this->addFulltextIndex('#__content', 'ftidx_introtext_fulltext', array('introtext','fulltext'));
		$this->addFulltextIndex('#__content', 'ftidx_title', 'title');
		$this->dropIndex('#__content', 'jos_content_state_idx');
		$this->dropIndex('#__content', 'title');
		$this->dropIndex('#__content', 'introtext');
		$this->dropIndex('#__content', 'jos_content_title_introtext_fulltext_ftidx');
		$this->addIndex('#__collections_votes', 'idx_item_id_user_id', array('item_id','user_id'));
		$this->dropIndex('#__collections_votes', 'idx_item_user');
		$this->addIndex('#__collections', 'idx_object_type_object_id', array('object_type','object_id'));
		$this->dropIndex('#__collections', 'idx_objectified');
		$this->addIndex('#__collections', 'idx_created_by', 'created_by');
		$this->dropIndex('#__collections', 'idx_createdby');
		$this->dropIndex('#__citations_authors', 'cid_auth_authid_uid');
		$this->dropIndex('#__citations_authors', 'authorid');
		$this->dropIndex('#__citations_authors', 'uidNumber');
		$this->dropIndex('#__citations', 'jos_citations_title_isbn_doi_abstract_ftidx');

		if (!$this->db->tableHasKey('#__cart_saved_addresses', 'uidx_uidNumber_saToFirst_saToLast_saAddress_saZip'))
                {
                        $query = "ALTER TABLE #__cart_saved_addresses ADD UNIQUE INDEX `uidx_uidNumber_saToFirst_saToLast_saAddress_saZip` (`uidNumber`,`saToFirst`,`saToLast`,`saAddress`(100),`saZip`)";
                        $this->db->setQuery($query);
                        $this->db->query();
                }

		$this->dropIndex('#__cart_saved_addresses', 'uidNumber');
		$this->addUniqueIndex('#__cart_memberships', "uidx_pId_crtId", array('pId','crtId'));
		$this->dropIndex('#__cart_memberships', 'pId');
		$this->addUniqueIndex('#__cart_carts', 'uidx_uidNumber', 'uidNumber');
		$this->dropIndex('#__cart_carts', 'uidNumber');
		$this->addFulltextIndex('#__blog_entries', 'ftidx_title', 'title');
		$this->dropIndex('#__blog_entries','title');
		$this->addFulltextIndex('#__blog_entries','ftidx_content','content');
		$this->dropIndex('#__blog_entries','content');
		$this->addFulltextIndex('#__answers_responses','ftidx_answer','answer');
		$this->dropIndex('#__answers_responses','answer');
		$this->addFulltextIndex('#__answers_questions','ftidx_question','question');
		$this->dropIndex('#__answers_questions','question');
		$this->addFulltextIndex('#__answers_questions','ftidx_subject','subject');
		$this->dropIndex('#__answers_questions','subject');
	}

	public function down()
	{
	}
}
