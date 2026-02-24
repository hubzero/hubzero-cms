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

        // Now do our best guess as to whether or not this has already been run
        $results = $this->db->getQuery(true)
            ->select(['alias', 'created'])
            ->from('#__faq')
            ->whereIn('alias', ['login', 'login2', 'login3'])
            ->loadObjectList();

        $need_to_run = false;

        if ($results && count($results) > 0) {
            foreach ($results as $r) {
                // Make them all match
                if ($r->created == '2010-03-25 02:26:40') {
                    $need_to_run = true;
                } else {
                    $need_to_run = false;
                    break;
                }
            }
        }

        if (!$need_to_run) {
            $this->setError('The timezone conversion appears to have already run. '
               . 'You should confirm this and mark this migration as run if necessary '
               . '(muse migration -fm --file=Migration20140305142845Core.php)', 'fatal');
            return false;
        }

        $updates = $this->getMigrationTargets();

        $this->callback('progress', 'init', array('Running ' . __CLASS__ . '.php:'));

        $total = count($updates);
        $i     = 1;

        foreach ($updates as $update) {
            $this->convertTableColumn($update[0], $update[1], $tz);

            $progress = round($i / $total * 100);
            $this->callback('progress', 'setProgress', array($progress));
            $i++;
        }

        $this->callback('progress', 'done');

        // Update configuration file with new timezone
        $configuration = file_get_contents(PATH_ROOT . DS . 'configuration.php');
        $configuration = preg_replace(
            '/(var \$offset[\s]*=[\s]*[\'"]*)UTC([\'"]*)/',
            '$1' . $tz . '$2',
            $configuration
        );
        file_put_contents(PATH_ROOT . DS . 'configuration.php', $configuration);
    }

    /**
     * Convert a column's values from a specific timezone to UTC
     *
     * @param   string  $table   Table name
     * @param   string  $column  Column name
     * @param   string  $fromTz  Original timezone
     * @return  void
     */
    protected function convertTableColumn($table, $column, $fromTz)
    {
        if (!$this->db->tableExists($table) || !$this->db->tableHasField($table, $column)) {
            return;
        }

        // Get distinct values
        $query = $this->db->getQuery(true)
            ->select($column)
            ->distinct()
            ->from($table)
            ->where($column, 'IS NOT', Expression::raw('NULL'))
            ->where($column, '!=', '0000-00-00 00:00:00');

        $dates = $query->loadColumn();

        if (empty($dates)) {
            return;
        }

        $serverTz = new \DateTimeZone($fromTz);
        $utcTz    = new \DateTimeZone('UTC');

        foreach ($dates as $date) {
            try {
                $dt = new \DateTime($date, $serverTz);
                $dt->setTimezone($utcTz);
                $newDate = $dt->format('Y-m-d H:i:s');

                if ($newDate != $date) {
                    $this->db->getQuery(true)
                        ->update($table)
                        ->set([$column => $newDate])
                        ->where($column, '=', $date)
                        ->execute();
                }
            } catch (\Exception $e) {
                // Ignore invalid dates
                continue;
            }
        }
    }

    /**
     * Get list of tables and columns to update
     *
     * @return  array
     */
    protected function getMigrationTargets()
    {
        return [
            ['#__abuse_reports', 'created'],
            ['#__abuse_reports', 'reviewed'],
            ['#__announcements', 'created'],
            ['#__announcements', 'publish_up'],
            ['#__announcements', 'publish_down'],
            ['#__answers_questions', 'created'],
            ['#__answers_questions_log', 'expires'],
            ['#__answers_responses', 'created'],
            ['#__author_roles', 'created'],
            ['#__author_roles', 'modified'],
            ['#__author_stats', 'datetime'],
            ['#__banner_clients', 'checked_out_time'],
            ['#__banner_tracks', 'track_date'],
            ['#__banners', 'checked_out_time'],
            ['#__banners', 'publish_up'],
            ['#__banners', 'publish_down'],
            ['#__banners', 'reset'],
            ['#__banners', 'created'],
            ['#__blog_comments', 'created'],
            ['#__blog_entries', 'created'],
            ['#__blog_entries', 'publish_up'],
            ['#__blog_entries', 'publish_down'],
            ['#__cart', 'added'],
            ['#__cart_carts', 'crtCreated'],
            ['#__cart_carts', 'crtLastUpdated'],
            ['#__cart_memberships', 'crtmExpires'],
            ['#__cart_transactions', 'tCreated'],
            ['#__cart_transactions', 'tLastUpdated'],
            ['#__citations', 'created'],
            ['#__citations', 'date_submit'],
            ['#__citations', 'date_accept'],
            ['#__citations', 'date_publish'],
            ['#__collections', 'created'],
            ['#__collections_assets', 'created'],
            ['#__collections_following', 'created'],
            ['#__collections_items', 'created'],
            ['#__collections_items', 'modified'],
            ['#__collections_posts', 'created'],
            ['#__collections_votes', 'voted'],
            ['#__comments', 'added'],
            ['#__contact_details', 'checked_out_time'],
            ['#__contact_details', 'created'],
            ['#__contact_details', 'modified'],
            ['#__contact_details', 'publish_up'],
            ['#__contact_details', 'publish_down'],
            ['#__courses', 'created'],
            ['#__courses_announcements', 'created'],
            ['#__courses_announcements', 'publish_up'],
            ['#__courses_announcements', 'publish_down'],
            ['#__courses_asset_groups', 'created'],
            ['#__courses_asset_views', 'viewed'],
            ['#__courses_assets', 'created'],
            ['#__courses_form_respondent_progress', 'submitted'],
            ['#__courses_log', 'timestamp'],
            ['#__courses_member_badges', 'earned_on'],
            ['#__courses_member_badges', 'action_on'],
            ['#__courses_member_notes', 'created'],
            ['#__courses_members', 'enrolled'],
            ['#__courses_members', 'first_visit'],
            ['#__courses_offering_section_codes', 'created'],
            ['#__courses_offering_section_codes', 'expires'],
            ['#__courses_offering_section_codes', 'redeemed'],
            ['#__courses_offering_section_dates', 'publish_up'],
            ['#__courses_offering_section_dates', 'publish_down'],
            ['#__courses_offering_section_dates', 'created'],
            ['#__courses_offering_sections', 'start_date'],
            ['#__courses_offering_sections', 'end_date'],
            ['#__courses_offering_sections', 'publish_up'],
            ['#__courses_offering_sections', 'publish_down'],
            ['#__courses_offering_sections', 'created'],
            ['#__courses_offerings', 'publish_up'],
            ['#__courses_offerings', 'publish_down'],
            ['#__courses_offerings', 'created'],
            ['#__courses_page_hits', 'datetime'],
            ['#__courses_reviews', 'created'],
            ['#__courses_reviews', 'modified'],
            ['#__courses_units', 'created'],
            ['#__cron_jobs', 'last_run'],
            ['#__cron_jobs', 'next_run'],
            ['#__cron_jobs', 'created'],
            ['#__cron_jobs', 'modified'],
            ['#__event_registration', 'submitted'],
            ['#__events', 'created'],
            ['#__events', 'modified'],
            ['#__events', 'checked_out_time'],
            ['#__events', 'publish_up'],
            ['#__events', 'publish_down'],
            ['#__events', 'registerby'],
            ['#__events_calendars', 'last_fetched'],
            ['#__events_calendars', 'last_fetched_attempt'],
            ['#__events_pages', 'created'],
            ['#__events_pages', 'modified'],
            ['#__faq', 'created'],
            ['#__faq', 'modified'],
            ['#__faq', 'checked_out_time'],
            ['#__faq_comments', 'created'],
            ['#__feature_history', 'featured'],
            ['#__feedback', 'date'],
            ['#__forum_categories', 'created'],
            ['#__forum_categories', 'modified'],
            ['#__forum_posts', 'created'],
            ['#__forum_posts', 'modified'],
            ['#__forum_posts', 'last_activity'],
            ['#__forum_sections', 'created'],
            ['#__item_comments', 'created'],
            ['#__item_comments', 'modified'],
            ['#__item_votes', 'created'],
            ['#__jobs_applications', 'applied'],
            ['#__jobs_applications', 'withdrawn'],
            ['#__jobs_employers', 'added'],
            ['#__jobs_openings', 'added'],
            ['#__jobs_openings', 'edited'],
            ['#__jobs_openings', 'closedate'],
            ['#__jobs_openings', 'opendate'],
            ['#__jobs_openings', 'startdate'],
            ['#__jobs_resumes', 'created'],
            ['#__jobs_seekers', 'updated'],
            ['#__jobs_shortlist', 'added'],
            ['#__jobs_stats', 'lastviewed'],
            ['#__licenses', 'created'],
            ['#__licenses', 'modified'],
            ['#__licenses_users', 'created'],
            ['#__market_history', 'date'],
            ['#__media_tracking', 'current_position_timestamp'],
            ['#__media_tracking', 'farthest_position_timestamp'],
            ['#__messages', 'date_time'],
            ['#__newsfeeds', 'checked_out_time'],
            ['#__newsfeeds', 'created'],
            ['#__newsfeeds', 'modified'],
            ['#__newsfeeds', 'publish_up'],
            ['#__newsfeeds', 'publish_down'],
            ['#__newsletter_mailing_recipient_actions', 'date'],
            ['#__newsletter_mailing_recipients', 'date_added'],
            ['#__newsletter_mailing_recipients', 'date_sent'],
            ['#__newsletter_mailinglist_emails', 'date_added'],
            ['#__newsletter_mailinglist_emails', 'date_confirmed'],
            ['#__newsletter_mailings', 'date'],
            ['#__newsletters', 'created'],
            ['#__newsletters', 'modified'],
            ['#__oauthp_nonces', 'created'],
            ['#__oauthp_tokens', 'created'],
            ['#__orders', 'ordered'],
            ['#__orders', 'status_changed'],
            ['#__poll_date', 'date'],
            ['#__polls', 'checked_out_time'],
            ['#__project_activity', 'recorded'],
            ['#__project_comments', 'created'],
            ['#__project_logs', 'time'],
            ['#__project_microblog', 'posted'],
            ['#__project_owners', 'added'],
            ['#__project_owners', 'lastvisit'],
            ['#__project_owners', 'prev_visit'],
            ['#__project_public_stamps', 'expires'],
            ['#__project_public_stamps', 'created'],
            ['#__project_remote_files', 'created'],
            ['#__project_remote_files', 'modified'],
            ['#__project_remote_files', 'synced'],
            ['#__project_remote_files', 'remote_modified'],
            ['#__project_stats', 'processed'],
            ['#__project_todo', 'created'],
            ['#__project_todo', 'duedate'],
            ['#__project_todo', 'closed'],
            ['#__projects', 'created'],
            ['#__projects', 'modified'],
            ['#__recent_tools', 'created'],
            ['#__redirect_links', 'created_date'],
            ['#__redirect_links', 'modified_date'],
            ['#__resource_ratings', 'created'],
            ['#__resource_sponsors', 'created'],
            ['#__resource_sponsors', 'modified'],
            ['#__resource_taxonomy_audience', 'added'],
            ['#__resources', 'created'],
            ['#__resources', 'modified'],
            ['#__resources', 'publish_up'],
            ['#__resources', 'publish_down'],
            ['#__resources', 'checked_out_time'],
            ['#__selected_quotes', 'date'],
            ['#__sites', 'checked_out_time'],
            ['#__sites', 'published_date'],
            ['#__store', 'created'],
            ['#__support_comments', 'created'],
            ['#__support_queries', 'created'],
            ['#__support_tickets', 'created'],
            ['#__support_tickets', 'closed'],
            ['#__tags_log', 'timestamp'],
            ['#__tags_object', 'taggedon'],
            ['#__tags_substitute', 'created'],
            ['#__tool', 'registered'],
            ['#__tool', 'state_changed'],
            ['#__tool_statusviews', 'viewed'],
            ['#__tool_version', 'released'],
            ['#__tool_version', 'unpublished'],
            ['#__user_notes', 'checked_out_time'],
            ['#__user_notes', 'created_time'],
            ['#__user_notes', 'modified_time'],
            ['#__user_notes', 'review_time'],
            ['#__user_notes', 'publish_up'],
            ['#__user_notes', 'publish_down'],
            ['#__users_password_history', 'created'],
            ['#__users_password_history', 'invalidated'],
            ['#__users_points_services', 'changed'],
            ['#__users_points_subscriptions', 'added'],
            ['#__users_points_subscriptions', 'updated'],
            ['#__users_points_subscriptions', 'expires'],
            ['#__users_transactions', 'created'],
            ['#__vote_log', 'voted'],
            ['#__wiki_attachments', 'created'],
            ['#__wiki_comments', 'created'],
            ['#__wiki_log', 'timestamp'],
            ['#__wiki_page', 'created'],
            ['#__wiki_page', 'modified'],
            ['#__wiki_page_links', 'timestamp'],
            ['#__wiki_version', 'created'],
            ['#__wishlist', 'created'],
            ['#__wishlist_implementation', 'created'],
            ['#__wishlist_item', 'proposed'],
            ['#__wishlist_item', 'granted'],
            ['#__wishlist_item', 'due'],
            ['#__wishlist_vote', 'voted'],
            ['#__wishlist_vote', 'due'],
            ['#__xfavorites', 'faved'],
            ['#__xgroups', 'created'],
            ['#__xgroups_log', 'timestamp'],
            ['#__xgroups_pages_hits', 'date'],
            ['#__xgroups_pages_hits', 'datetime'],
            ['#__xgroups_reasons', 'date'],
            ['#__xmessage', 'created'],
            ['#__xmessage_recipient', 'created'],
            ['#__xmessage_recipient', 'expires'],
            ['#__xprofiles', 'registerDate'],
            ['#__xprofiles', 'modifiedDate'],
            ['#__xprofiles_tags', 'taggedon'],
        ];
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
