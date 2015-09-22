<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script to add com_publications component again (asset code having been added to 1.2.0 branch)
 **/
class Migration20150922095207ComStorefront extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$params = array(
                        "enabled" => "1",
                        "autoapprove" => "1",
                        "autoapproved_users" => "",
                        "email" => "0",
                        "default_category" => "dataset",
                        "defaultpic" => "/components/com_publications/assets/img/resource_thumb.gif",
                        "toolpic" => "/components/com_publications/assets/img/tool_thumb.gif",
                        "video_thumb" => "/components/com_publications/images/video_thumb.gif",
                        "gallery_thumb" => "/components/com_publications/images/gallery_thumb.gif",
                        "webpath" => "/site/publications",
                        "aboutdoi" => "",
                        "doi_shoulder" => "",
                        "doi_prefix" => "",
                        "doi_service" => "",
                        "doi_userpw" => "",
                        "doi_xmlschema" => "",
                        "doi_publisher" => "",
                        "doi_resolve" => "http://dx.doi.org/",
                        "doi_verify" => "http://n2t.net/ezid/id/",
                        "supportedtag" => "",
                        "supportedlink" => "",
                        "google_id" => "",
                        "show_authors" => "1",
                        "show_ranking" => "1",
                        "show_rating" => "1",
                        "show_date" => "3",
                        "show_citation" => "1",
                        "panels" => "content, description, authors, audience, gallery, tags, access, license, notes",
                        "suggest_licence" => "0",
                        "show_tags" => "1",
                        "show_metadata" => "1",
                        "show_notes" => "1",
                        "show_license" => "1",
                        "show_access" => "0",
                        "show_gallery" => "1",
                        "show_audience" => "0",
                        "audiencelink" => "",
                        "documentation" => "/kb/publications",
                        "deposit_terms" => "/legal/termsofdeposit",
                        "dbcheck" => "0",
                        "repository" => "0",
                        "aip_path" => "/srv/AIP"
                );

		self::addComponentEntry('Publications', 'com_publications', 1, $params);
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		self::deleteComponentEntry('Publications');
	}
}
