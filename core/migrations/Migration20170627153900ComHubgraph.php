<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for removing triggers for com_hubgraph
 **/
class Migration20170627153900ComHubgraph extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->runAsRoot())
		{
			$this->setError('Failed to run with the necessary privileges.', 'warning');
			return false;
		}

		$triggers = array(
			//Answers
			'hg_jos_answers_questions_insert_trigger',
			'hg_jos_answers_questions_update_trigger',
			'hg_jos_answers_questions_delete_trigger',
			'hg_jos_answers_responses_insert_trigger',
			'hg_jos_answers_responses_update_trigger',
			'hg_jos_answers_responses_delete_trigger',
			// Authors
			'hg_jos_author_assoc_insert_trigger',
			'hg_jos_author_assoc_update_trigger',
			'hg_jos_author_assoc_delete_trigger',
			// Blog
			'hg_jos_blog_comments_insert_trigger',
			'hg_jos_blog_comments_update_trigger',
			'hg_jos_blog_comments_delete_trigger',
			'hg_jos_blog_entries_insert_trigger',
			'hg_jos_blog_entries_update_trigger',
			'hg_jos_blog_entries_delete_trigger',
			// Categories
			'hg_jos_categories_insert_trigger',
			'hg_jos_categories_update_trigger',
			'hg_jos_categories_delete_trigger',
			// Citations
			'hg_jos_citations_insert_trigger',
			'hg_jos_citations_update_trigger',
			'hg_jos_citations_delete_trigger',
			// Collections
			'hg_jos_collections_insert_trigger',
			'hg_jos_collections_update_trigger',
			'hg_jos_collections_delete_trigger',
			'hg_jos_collections_items_insert_trigger',
			'hg_jos_collections_items_update_trigger',
			'hg_jos_collections_items_delete_trigger',
			'hg_jos_collections_posts_insert_trigger',
			'hg_jos_collections_posts_update_trigger',
			'hg_jos_collections_posts_delete_trigger',
			// Content
			'hg_jos_content_insert_trigger',
			'hg_jos_content_update_trigger',
			'hg_jos_content_delete_trigger',
			// Document
			'hg_jos_document_resource_rel_insert_trigger',
			'hg_jos_document_resource_rel_update_trigger',
			'hg_jos_document_resource_rel_delete_trigger',
			'hg_jos_document_text_data_insert_trigger',
			'hg_jos_document_text_data_update_trigger',
			'hg_jos_document_text_data_delete_trigger',
			// Events
			'hg_jos_events_insert_trigger',
			'hg_jos_events_update_trigger',
			'hg_jos_events_delete_trigger',
			// Forum
			'hg_jos_forum_categories_insert_trigger',
			'hg_jos_forum_categories_update_trigger',
			'hg_jos_forum_categories_delete_trigger',
			'hg_jos_forum_posts_insert_trigger',
			'hg_jos_forum_posts_update_trigger',
			'hg_jos_forum_posts_delete_trigger',
			'hg_jos_forum_sections_insert_trigger',
			'hg_jos_forum_sections_update_trigger',
			'hg_jos_forum_sections_delete_trigger',
			// KB
			'hg_jos_faq_insert_trigger',
			'hg_jos_faq_update_trigger',
			'hg_jos_faq_delete_trigger',
			// Publications
			'hg_jos_publication_authors_insert_trigger',
			'hg_jos_publication_authors_update_trigger',
			'hg_jos_publication_authors_delete_trigger',
			'hg_jos_publication_categories_insert_trigger',
			'hg_jos_publication_categories_update_trigger',
			'hg_jos_publication_categories_delete_trigger',
			'hg_jos_publication_versions_insert_trigger',
			'hg_jos_publication_versions_update_trigger',
			'hg_jos_publication_versions_delete_trigger',
			'hg_jos_publications_insert_trigger',
			'hg_jos_publications_update_trigger',
			'hg_jos_publications_delete_trigger',
			// Resources
			'hg_jos_resource_assoc_insert_trigger',
			'hg_jos_resource_assoc_update_trigger',
			'hg_jos_resource_assoc_delete_trigger',
			'hg_jos_resource_taxonomy_audience_insert_trigger',
			'hg_jos_resource_taxonomy_audience_update_trigger',
			'hg_jos_resource_taxonomy_audience_delete_trigger',
			'hg_jos_resource_types_insert_trigger',
			'hg_jos_resource_types_update_trigger',
			'hg_jos_resource_types_delete_trigger',
			'hg_jos_resources_insert_trigger',
			'hg_jos_resources_update_trigger',
			'hg_jos_resources_delete_trigger',
			// Tags
			'hg_jos_tags_insert_trigger',
			'hg_jos_tags_update_trigger',
			'hg_jos_tags_delete_trigger',
			'hg_jos_tags_object_insert_trigger',
			'hg_jos_tags_object_update_trigger',
			'hg_jos_tags_object_delete_trigger',
			'hg_jos_tags_substitute_insert_trigger',
			'hg_jos_tags_substitute_update_trigger',
			'hg_jos_tags_substitute_delete_trigger',
			// Tools
			'hg_jos_tool_insert_trigger',
			'hg_jos_tool_update_trigger',
			'hg_jos_tool_delete_trigger',
			// Users
			'hg_jos_users_insert_trigger',
			'hg_jos_users_update_trigger',
			'hg_jos_users_delete_trigger',
			'hg_jos_users_transactions_insert_trigger',
			'hg_jos_users_transactions_update_trigger',
			'hg_jos_users_transactions_delete_trigger',
			// Votes
			'hg_jos_vote_log_insert_trigger',
			'hg_jos_vote_log_update_trigger',
			'hg_jos_vote_log_delete_trigger',
			// Wiki pages
			'hg_jos_wiki_version_insert_trigger',
			'hg_jos_wiki_version_update_trigger',
			'hg_jos_wiki_version_delete_trigger',
			'hg_jos_wiki_page_insert_trigger',
			'hg_jos_wiki_page_update_trigger',
			'hg_jos_wiki_page_delete_trigger',
			'hg_jos_wiki_page_author_insert_trigger',
			'hg_jos_wiki_page_author_update_trigger',
			'hg_jos_wiki_page_author_delete_trigger',
			// Wishlist
			'hg_jos_wishlist_vote_insert_trigger',
			'hg_jos_wishlist_vote_update_trigger',
			'hg_jos_wishlist_vote_delete_trigger',
			'hg_jos_wishlist_item_insert_trigger',
			'hg_jos_wishlist_item_update_trigger',
			'hg_jos_wishlist_item_delete_trigger',
			'hg_jos_wishlist_insert_trigger',
			'hg_jos_wishlist_update_trigger',
			'hg_jos_wishlist_delete_trigger',
			// Groups
			'hg_jos_xgroups_insert_trigger',
			'hg_jos_xgroups_update_trigger',
			'hg_jos_xgroups_delete_trigger',
			// Profiles
			'hg_jos_xprofiles_insert_trigger',
			'hg_jos_xprofiles_update_trigger',
			'hg_jos_xprofiles_delete_trigger',
			'hg_jos_xprofiles_bio_insert_trigger',
			'hg_jos_xprofiles_bio_update_trigger',
			'hg_jos_xprofiles_bio_delete_trigger'
		);

		foreach ($triggers as $trigger)
		{
			$query = "DROP TRIGGER IF EXISTS `" . $trigger . "`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->runAsRoot())
		{
			$this->setError('Failed to run with the necessary privileges.', 'warning');
			return false;
		}

		// Create triggers...
	}
}
