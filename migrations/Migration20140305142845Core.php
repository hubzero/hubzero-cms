<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for converting dates from server timezone to UTC
 **/
class Migration20140305142845Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// First, get the current timezone according to PHP
		$tz = date_default_timezone_get();

		// Now, run a test to ensure that the MySQL tz info is setup
		$query = "SELECT CONVERT_TZ('2014-01-01 12:00:00', '{$tz}', 'UTC')";
		$this->db->setQuery($query);
		$result = $this->db->loadResult();

		if (!$result)
		{
			$this->setError('Missing MySQL timezone table. Please ensure the necessary timezone setup migration has been run.', 'fatal');
			return false;
		}

		// Now do our best guess as to whether or not this has already been run
		$query = "SELECT `alias`, `created` FROM `#__faq` WHERE `alias` IN ('login', 'login2', 'login3')";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		$need_to_run = false;

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				// Make them all match
				if ($r->created == '2010-03-25 02:26:40')
				{
					$need_to_run = true;
				}
				else
				{
					$need_to_run = false;
					break;
				}
			}
		}

		if (!$need_to_run)
		{
			$this->setError('The timezone conversion appears to have already run. You should confirm this and mark this migration as run if necessary (muse migration -fm --file=Migration20140305142845Core.php)', 'fatal');
			return false;
		}

		$updates = array(
			"UPDATE `#__abuse_reports` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__abuse_reports` SET `reviewed` = CONVERT_TZ(`reviewed`, '{$tz}', 'UTC') WHERE `reviewed` IS NOT NULL AND `reviewed` != '0000-00-00 00:00:00';",
			"UPDATE `#__announcements` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__announcements` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__announcements` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__answers_questions` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__answers_questions_log` SET `expires` = CONVERT_TZ(`expires`, '{$tz}', 'UTC') WHERE `expires` IS NOT NULL AND `expires` != '0000-00-00 00:00:00';",
			"UPDATE `#__answers_responses` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__author_roles` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__author_roles` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__author_stats` SET `datetime` = CONVERT_TZ(`datetime`, '{$tz}', 'UTC') WHERE `datetime` IS NOT NULL AND `datetime` != '0000-00-00 00:00:00';",
			"UPDATE `#__banner_clients` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__banner_tracks` SET `track_date` = CONVERT_TZ(`track_date`, '{$tz}', 'UTC') WHERE `track_date` IS NOT NULL AND `track_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__banners` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__banners` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__banners` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__banners` SET `reset` = CONVERT_TZ(`reset`, '{$tz}', 'UTC') WHERE `reset` IS NOT NULL AND `reset` != '0000-00-00 00:00:00';",
			"UPDATE `#__banners` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__blog_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__blog_entries` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__blog_entries` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__blog_entries` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart_carts` SET `crtCreated` = CONVERT_TZ(`crtCreated`, '{$tz}', 'UTC') WHERE `crtCreated` IS NOT NULL AND `crtCreated` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart_carts` SET `crtLastUpdated` = CONVERT_TZ(`crtLastUpdated`, '{$tz}', 'UTC') WHERE `crtLastUpdated` IS NOT NULL AND `crtLastUpdated` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart_memberships` SET `crtmExpires` = CONVERT_TZ(`crtmExpires`, '{$tz}', 'UTC') WHERE `crtmExpires` IS NOT NULL AND `crtmExpires` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart_transactions` SET `tCreated` = CONVERT_TZ(`tCreated`, '{$tz}', 'UTC') WHERE `tCreated` IS NOT NULL AND `tCreated` != '0000-00-00 00:00:00';",
			"UPDATE `#__cart_transactions` SET `tLastUpdated` = CONVERT_TZ(`tLastUpdated`, '{$tz}', 'UTC') WHERE `tLastUpdated` IS NOT NULL AND `tLastUpdated` != '0000-00-00 00:00:00';",
			"UPDATE `#__citations` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__citations` SET `date_submit` = CONVERT_TZ(`date_submit`, '{$tz}', 'UTC') WHERE `date_submit` IS NOT NULL AND `date_submit` != '0000-00-00 00:00:00';",
			"UPDATE `#__citations` SET `date_accept` = CONVERT_TZ(`date_accept`, '{$tz}', 'UTC') WHERE `date_accept` IS NOT NULL AND `date_accept` != '0000-00-00 00:00:00';",
			"UPDATE `#__citations` SET `date_publish` = CONVERT_TZ(`date_publish`, '{$tz}', 'UTC') WHERE `date_publish` IS NOT NULL AND `date_publish` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_assets` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_following` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_items` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_items` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_posts` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__collections_votes` SET `voted` = CONVERT_TZ(`voted`, '{$tz}', 'UTC') WHERE `voted` IS NOT NULL AND `voted` != '0000-00-00 00:00:00';",
			"UPDATE `#__comments` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__contact_details` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__contact_details` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__contact_details` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__contact_details` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__contact_details` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_announcements` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_announcements` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_announcements` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_asset_groups` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_asset_views` SET `viewed` = CONVERT_TZ(`viewed`, '{$tz}', 'UTC') WHERE `viewed` IS NOT NULL AND `viewed` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_assets` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_form_respondent_progress` SET `submitted` = CONVERT_TZ(`submitted`, '{$tz}', 'UTC') WHERE `submitted` IS NOT NULL AND `submitted` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_log` SET `timestamp` = CONVERT_TZ(`timestamp`, '{$tz}', 'UTC') WHERE `timestamp` IS NOT NULL AND `timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_member_badges` SET `earned_on` = CONVERT_TZ(`earned_on`, '{$tz}', 'UTC') WHERE `earned_on` IS NOT NULL AND `earned_on` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_member_badges` SET `action_on` = CONVERT_TZ(`action_on`, '{$tz}', 'UTC') WHERE `action_on` IS NOT NULL AND `action_on` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_member_notes` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_members` SET `enrolled` = CONVERT_TZ(`enrolled`, '{$tz}', 'UTC') WHERE `enrolled` IS NOT NULL AND `enrolled` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_members` SET `first_visit` = CONVERT_TZ(`first_visit`, '{$tz}', 'UTC') WHERE `first_visit` IS NOT NULL AND `first_visit` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_codes` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_codes` SET `expires` = CONVERT_TZ(`expires`, '{$tz}', 'UTC') WHERE `expires` IS NOT NULL AND `expires` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_codes` SET `redeemed` = CONVERT_TZ(`redeemed`, '{$tz}', 'UTC') WHERE `redeemed` IS NOT NULL AND `redeemed` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_dates` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_dates` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_section_dates` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_sections` SET `start_date` = CONVERT_TZ(`start_date`, '{$tz}', 'UTC') WHERE `start_date` IS NOT NULL AND `start_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_sections` SET `end_date` = CONVERT_TZ(`end_date`, '{$tz}', 'UTC') WHERE `end_date` IS NOT NULL AND `end_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_sections` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_sections` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offering_sections` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offerings` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offerings` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_offerings` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_page_hits` SET `datetime` = CONVERT_TZ(`datetime`, '{$tz}', 'UTC') WHERE `datetime` IS NOT NULL AND `datetime` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_reviews` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_reviews` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__courses_units` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__cron_jobs` SET `last_run` = CONVERT_TZ(`last_run`, '{$tz}', 'UTC') WHERE `last_run` IS NOT NULL AND `last_run` != '0000-00-00 00:00:00';",
			"UPDATE `#__cron_jobs` SET `next_run` = CONVERT_TZ(`next_run`, '{$tz}', 'UTC') WHERE `next_run` IS NOT NULL AND `next_run` != '0000-00-00 00:00:00';",
			"UPDATE `#__cron_jobs` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__cron_jobs` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__event_registration` SET `submitted` = CONVERT_TZ(`submitted`, '{$tz}', 'UTC') WHERE `submitted` IS NOT NULL AND `submitted` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__events` SET `registerby` = CONVERT_TZ(`registerby`, '{$tz}', 'UTC') WHERE `registerby` IS NOT NULL AND `registerby` != '0000-00-00 00:00:00';",
			"UPDATE `#__events_calendars` SET `last_fetched` = CONVERT_TZ(`last_fetched`, '{$tz}', 'UTC') WHERE `last_fetched` IS NOT NULL AND `last_fetched` != '0000-00-00 00:00:00';",
			"UPDATE `#__events_calendars` SET `last_fetched_attempt` = CONVERT_TZ(`last_fetched_attempt`, '{$tz}', 'UTC') WHERE `last_fetched_attempt` IS NOT NULL AND `last_fetched_attempt` != '0000-00-00 00:00:00';",
			"UPDATE `#__events_pages` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__events_pages` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__faq` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__faq` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__faq` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__faq_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__feature_history` SET `featured` = CONVERT_TZ(`featured`, '{$tz}', 'UTC') WHERE `featured` IS NOT NULL AND `featured` != '0000-00-00 00:00:00';",
			"UPDATE `#__feedback` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_categories` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_categories` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_posts` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_posts` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_posts` SET `last_activity` = CONVERT_TZ(`last_activity`, '{$tz}', 'UTC') WHERE `last_activity` IS NOT NULL AND `last_activity` != '0000-00-00 00:00:00';",
			"UPDATE `#__forum_sections` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__item_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__item_comments` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__item_votes` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_applications` SET `applied` = CONVERT_TZ(`applied`, '{$tz}', 'UTC') WHERE `applied` IS NOT NULL AND `applied` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_applications` SET `withdrawn` = CONVERT_TZ(`withdrawn`, '{$tz}', 'UTC') WHERE `withdrawn` IS NOT NULL AND `withdrawn` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_employers` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_openings` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_openings` SET `edited` = CONVERT_TZ(`edited`, '{$tz}', 'UTC') WHERE `edited` IS NOT NULL AND `edited` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_openings` SET `closedate` = CONVERT_TZ(`closedate`, '{$tz}', 'UTC') WHERE `closedate` IS NOT NULL AND `closedate` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_openings` SET `opendate` = CONVERT_TZ(`opendate`, '{$tz}', 'UTC') WHERE `opendate` IS NOT NULL AND `opendate` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_openings` SET `startdate` = CONVERT_TZ(`startdate`, '{$tz}', 'UTC') WHERE `startdate` IS NOT NULL AND `startdate` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_resumes` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_seekers` SET `updated` = CONVERT_TZ(`updated`, '{$tz}', 'UTC') WHERE `updated` IS NOT NULL AND `updated` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_shortlist` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__jobs_stats` SET `lastviewed` = CONVERT_TZ(`lastviewed`, '{$tz}', 'UTC') WHERE `lastviewed` IS NOT NULL AND `lastviewed` != '0000-00-00 00:00:00';",
			"UPDATE `#__licenses` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__licenses` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__licenses_users` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__market_history` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__media_tracking` SET `current_position_timestamp` = CONVERT_TZ(`current_position_timestamp`, '{$tz}', 'UTC') WHERE `current_position_timestamp` IS NOT NULL AND `current_position_timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__media_tracking` SET `farthest_position_timestamp` = CONVERT_TZ(`farthest_position_timestamp`, '{$tz}', 'UTC') WHERE `farthest_position_timestamp` IS NOT NULL AND `farthest_position_timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__messages` SET `date_time` = CONVERT_TZ(`date_time`, '{$tz}', 'UTC') WHERE `date_time` IS NOT NULL AND `date_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsfeeds` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsfeeds` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsfeeds` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsfeeds` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsfeeds` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailing_recipient_actions` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailing_recipients` SET `date_added` = CONVERT_TZ(`date_added`, '{$tz}', 'UTC') WHERE `date_added` IS NOT NULL AND `date_added` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailing_recipients` SET `date_sent` = CONVERT_TZ(`date_sent`, '{$tz}', 'UTC') WHERE `date_sent` IS NOT NULL AND `date_sent` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailinglist_emails` SET `date_added` = CONVERT_TZ(`date_added`, '{$tz}', 'UTC') WHERE `date_added` IS NOT NULL AND `date_added` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailinglist_emails` SET `date_confirmed` = CONVERT_TZ(`date_confirmed`, '{$tz}', 'UTC') WHERE `date_confirmed` IS NOT NULL AND `date_confirmed` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletter_mailings` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletters` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__newsletters` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__oauthp_nonces` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__oauthp_tokens` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__orders` SET `ordered` = CONVERT_TZ(`ordered`, '{$tz}', 'UTC') WHERE `ordered` IS NOT NULL AND `ordered` != '0000-00-00 00:00:00';",
			"UPDATE `#__orders` SET `status_changed` = CONVERT_TZ(`status_changed`, '{$tz}', 'UTC') WHERE `status_changed` IS NOT NULL AND `status_changed` != '0000-00-00 00:00:00';",
			"UPDATE `#__poll_date` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__polls` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_activity` SET `recorded` = CONVERT_TZ(`recorded`, '{$tz}', 'UTC') WHERE `recorded` IS NOT NULL AND `recorded` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_logs` SET `time` = CONVERT_TZ(`time`, '{$tz}', 'UTC') WHERE `time` IS NOT NULL AND `time` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_microblog` SET `posted` = CONVERT_TZ(`posted`, '{$tz}', 'UTC') WHERE `posted` IS NOT NULL AND `posted` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_owners` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_owners` SET `lastvisit` = CONVERT_TZ(`lastvisit`, '{$tz}', 'UTC') WHERE `lastvisit` IS NOT NULL AND `lastvisit` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_owners` SET `prev_visit` = CONVERT_TZ(`prev_visit`, '{$tz}', 'UTC') WHERE `prev_visit` IS NOT NULL AND `prev_visit` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_public_stamps` SET `expires` = CONVERT_TZ(`expires`, '{$tz}', 'UTC') WHERE `expires` IS NOT NULL AND `expires` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_public_stamps` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_remote_files` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_remote_files` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_remote_files` SET `synced` = CONVERT_TZ(`synced`, '{$tz}', 'UTC') WHERE `synced` IS NOT NULL AND `synced` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_remote_files` SET `remote_modified` = CONVERT_TZ(`remote_modified`, '{$tz}', 'UTC') WHERE `remote_modified` IS NOT NULL AND `remote_modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_stats` SET `processed` = CONVERT_TZ(`processed`, '{$tz}', 'UTC') WHERE `processed` IS NOT NULL AND `processed` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_todo` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_todo` SET `duedate` = CONVERT_TZ(`duedate`, '{$tz}', 'UTC') WHERE `duedate` IS NOT NULL AND `duedate` != '0000-00-00 00:00:00';",
			"UPDATE `#__project_todo` SET `closed` = CONVERT_TZ(`closed`, '{$tz}', 'UTC') WHERE `closed` IS NOT NULL AND `closed` != '0000-00-00 00:00:00';",
			"UPDATE `#__projects` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__projects` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__recent_tools` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__redirect_links` SET `created_date` = CONVERT_TZ(`created_date`, '{$tz}', 'UTC') WHERE `created_date` IS NOT NULL AND `created_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__redirect_links` SET `modified_date` = CONVERT_TZ(`modified_date`, '{$tz}', 'UTC') WHERE `modified_date` IS NOT NULL AND `modified_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__resource_ratings` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__resource_sponsors` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__resource_sponsors` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__resource_taxonomy_audience` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__resources` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__resources` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__resources` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__resources` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__resources` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__selected_quotes` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__sites` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__sites` SET `published_date` = CONVERT_TZ(`published_date`, '{$tz}', 'UTC') WHERE `published_date` IS NOT NULL AND `published_date` != '0000-00-00 00:00:00';",
			"UPDATE `#__store` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__support_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__support_queries` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__support_tickets` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__support_tickets` SET `closed` = CONVERT_TZ(`closed`, '{$tz}', 'UTC') WHERE `closed` IS NOT NULL AND `closed` != '0000-00-00 00:00:00';",
			"UPDATE `#__tags_log` SET `timestamp` = CONVERT_TZ(`timestamp`, '{$tz}', 'UTC') WHERE `timestamp` IS NOT NULL AND `timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__tags_object` SET `taggedon` = CONVERT_TZ(`taggedon`, '{$tz}', 'UTC') WHERE `taggedon` IS NOT NULL AND `taggedon` != '0000-00-00 00:00:00';",
			"UPDATE `#__tags_substitute` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__tool` SET `registered` = CONVERT_TZ(`registered`, '{$tz}', 'UTC') WHERE `registered` IS NOT NULL AND `registered` != '0000-00-00 00:00:00';",
			"UPDATE `#__tool` SET `state_changed` = CONVERT_TZ(`state_changed`, '{$tz}', 'UTC') WHERE `state_changed` IS NOT NULL AND `state_changed` != '0000-00-00 00:00:00';",
			"UPDATE `#__tool_statusviews` SET `viewed` = CONVERT_TZ(`viewed`, '{$tz}', 'UTC') WHERE `viewed` IS NOT NULL AND `viewed` != '0000-00-00 00:00:00';",
			"UPDATE `#__tool_version` SET `released` = CONVERT_TZ(`released`, '{$tz}', 'UTC') WHERE `released` IS NOT NULL AND `released` != '0000-00-00 00:00:00';",
			"UPDATE `#__tool_version` SET `unpublished` = CONVERT_TZ(`unpublished`, '{$tz}', 'UTC') WHERE `unpublished` IS NOT NULL AND `unpublished` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `checked_out_time` = CONVERT_TZ(`checked_out_time`, '{$tz}', 'UTC') WHERE `checked_out_time` IS NOT NULL AND `checked_out_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `created_time` = CONVERT_TZ(`created_time`, '{$tz}', 'UTC') WHERE `created_time` IS NOT NULL AND `created_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `modified_time` = CONVERT_TZ(`modified_time`, '{$tz}', 'UTC') WHERE `modified_time` IS NOT NULL AND `modified_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `review_time` = CONVERT_TZ(`review_time`, '{$tz}', 'UTC') WHERE `review_time` IS NOT NULL AND `review_time` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `publish_up` = CONVERT_TZ(`publish_up`, '{$tz}', 'UTC') WHERE `publish_up` IS NOT NULL AND `publish_up` != '0000-00-00 00:00:00';",
			"UPDATE `#__user_notes` SET `publish_down` = CONVERT_TZ(`publish_down`, '{$tz}', 'UTC') WHERE `publish_down` IS NOT NULL AND `publish_down` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_password_history` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_password_history` SET `invalidated` = CONVERT_TZ(`invalidated`, '{$tz}', 'UTC') WHERE `invalidated` IS NOT NULL AND `invalidated` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_points_services` SET `changed` = CONVERT_TZ(`changed`, '{$tz}', 'UTC') WHERE `changed` IS NOT NULL AND `changed` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_points_subscriptions` SET `added` = CONVERT_TZ(`added`, '{$tz}', 'UTC') WHERE `added` IS NOT NULL AND `added` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_points_subscriptions` SET `updated` = CONVERT_TZ(`updated`, '{$tz}', 'UTC') WHERE `updated` IS NOT NULL AND `updated` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_points_subscriptions` SET `expires` = CONVERT_TZ(`expires`, '{$tz}', 'UTC') WHERE `expires` IS NOT NULL AND `expires` != '0000-00-00 00:00:00';",
			"UPDATE `#__users_transactions` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__vote_log` SET `voted` = CONVERT_TZ(`voted`, '{$tz}', 'UTC') WHERE `voted` IS NOT NULL AND `voted` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_attachments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_comments` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_log` SET `timestamp` = CONVERT_TZ(`timestamp`, '{$tz}', 'UTC') WHERE `timestamp` IS NOT NULL AND `timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_page` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_page` SET `modified` = CONVERT_TZ(`modified`, '{$tz}', 'UTC') WHERE `modified` IS NOT NULL AND `modified` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_page_links` SET `timestamp` = CONVERT_TZ(`timestamp`, '{$tz}', 'UTC') WHERE `timestamp` IS NOT NULL AND `timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__wiki_version` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_implementation` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_item` SET `proposed` = CONVERT_TZ(`proposed`, '{$tz}', 'UTC') WHERE `proposed` IS NOT NULL AND `proposed` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_item` SET `granted` = CONVERT_TZ(`granted`, '{$tz}', 'UTC') WHERE `granted` IS NOT NULL AND `granted` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_item` SET `due` = CONVERT_TZ(`due`, '{$tz}', 'UTC') WHERE `due` IS NOT NULL AND `due` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_vote` SET `voted` = CONVERT_TZ(`voted`, '{$tz}', 'UTC') WHERE `voted` IS NOT NULL AND `voted` != '0000-00-00 00:00:00';",
			"UPDATE `#__wishlist_vote` SET `due` = CONVERT_TZ(`due`, '{$tz}', 'UTC') WHERE `due` IS NOT NULL AND `due` != '0000-00-00 00:00:00';",
			"UPDATE `#__xfavorites` SET `faved` = CONVERT_TZ(`faved`, '{$tz}', 'UTC') WHERE `faved` IS NOT NULL AND `faved` != '0000-00-00 00:00:00';",
			"UPDATE `#__xgroups` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__xgroups_log` SET `timestamp` = CONVERT_TZ(`timestamp`, '{$tz}', 'UTC') WHERE `timestamp` IS NOT NULL AND `timestamp` != '0000-00-00 00:00:00';",
			"UPDATE `#__xgroups_pages_hits` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__xgroups_pages_hits` SET `datetime` = CONVERT_TZ(`datetime`, '{$tz}', 'UTC') WHERE `datetime` IS NOT NULL AND `datetime` != '0000-00-00 00:00:00';",
			"UPDATE `#__xgroups_reasons` SET `date` = CONVERT_TZ(`date`, '{$tz}', 'UTC') WHERE `date` IS NOT NULL AND `date` != '0000-00-00 00:00:00';",
			"UPDATE `#__xmessage` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__xmessage_recipient` SET `created` = CONVERT_TZ(`created`, '{$tz}', 'UTC') WHERE `created` IS NOT NULL AND `created` != '0000-00-00 00:00:00';",
			"UPDATE `#__xmessage_recipient` SET `expires` = CONVERT_TZ(`expires`, '{$tz}', 'UTC') WHERE `expires` IS NOT NULL AND `expires` != '0000-00-00 00:00:00';",
			"UPDATE `#__xprofiles` SET `registerDate` = CONVERT_TZ(`registerDate`, '{$tz}', 'UTC') WHERE `registerDate` IS NOT NULL AND `registerDate` != '0000-00-00 00:00:00';",
			"UPDATE `#__xprofiles` SET `modifiedDate` = CONVERT_TZ(`modifiedDate`, '{$tz}', 'UTC') WHERE `modifiedDate` IS NOT NULL AND `modifiedDate` != '0000-00-00 00:00:00';",
			"UPDATE `#__xprofiles_tags` SET `taggedon` = CONVERT_TZ(`taggedon`, '{$tz}', 'UTC') WHERE `taggedon` IS NOT NULL AND `taggedon` != '0000-00-00 00:00:00';"
		);

		$this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));

		$total = count($updates);
		$i     = 1;

		foreach ($updates as $update)
		{
			preg_match('/UPDATE `(#__[[:alpha:]_]*)` SET `([[:alpha:]_]*)`/', $update, $matches);
			$table  = $matches[1];
			$column = $matches[2];

			if ($this->db->tableExists($table) && $this->db->tableHasField($table, $column))
			{
				$this->db->setQuery($update);
				$this->db->query();

				$progress = round($i/$total*100);
				$this->callback('progress', 'setProgress', array($progress));
				$i++;
			}
		}

		$this->callback('progress', 'done');

		// Update configuration file with new timezone
		$configuration = file_get_contents(JPATH_ROOT . DS . 'configuration.php');
		$configuration = preg_replace('/(var \$offset[\s]*=[\s]*[\'"]*)UTC([\'"]*)/', '$1'.$tz.'$2', $configuration);
		file_put_contents(JPATH_ROOT . DS . 'configuration.php', $configuration);
	}
}