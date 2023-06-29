<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for wiki pages
 */
class plgDeidentifyMembers extends \Hubzero\Plugin\Plugin {

    public function runSelectQuery($query) {
        $db = \App::get('db');
        $db->setQuery($query);
        $objRows = $db->loadObjectList();

        // json_encode: returns a string containing the JSON representation from the mySQL -> json_decode: Returns the value encoded in json in appropriate PHP type
        $objString = json_encode($objRows, true);
        return json_decode($objString, true);
    }

    public function runInsertQuery($query, $vars) {
        $db = \App::get('db');
        $db->prepare($query);
        $db->bind($vars);
        return $db->execute();
    }

    public function runUpdateOrDeleteQuery($query) {
        $db = \App::get('db');
        $db->setQuery($query);
        return $db->query();
    }


	public function onUserDeidentify($user_id) {
        $db = \App::get('db');

        // PURPOSE: Find username, id, email from jos_users table
        $select_UsersById_Query = "SELECT id, username, email, password FROM `#__users` WHERE id='" . $user_id . "';";
        $userJsonObj = $this->runSelectQuery($select_UsersById_Query);

        $userId = $user_id;
        $userEmail = "";
        $userName = "";
        $userLinkId = "";

        if ($userJsonObj) {
            $userId = $userJsonObj[0]['id'];
            $userEmail = $userJsonObj[0]['email'];
            $userName = $userJsonObj[0]['username'];
        }

        // Creating New Credentials
        $anonUserId = "anon_user_" . $userId;
        $anonPassword = "anon_password_" . $userId;
        $anonUserName = "anon_username_" . $userId;

        // PURPOSE: Find auth link id from jos_auth_link table
        $select_AuthLink_Query = "SELECT id, user_id FROM `#__auth_link` WHERE user_id='" . $user_id . "';";
        $authLinkJsonObj = $this->runSelectQuery($select_AuthLink_Query);
        if ($authLinkJsonObj) {
            $userLinkId = $authLinkJsonObj[0]['id'];
        }

        print_r($userId); // 1005
        print_r("<br>");
        print_r($userEmail); // membertest1@gmail.com
        print_r("<br>");
        print_r($userName); // membertest1
        print_r("<br>");
        print_r($userLinkId);
        print_r("<br>");


        // GENERAL INSERT VARIABLES
        $insert_userId_Vars = array($user_id);

        // ======= Sanitation Queries // deletes, updates, inserts =======
        // NOTE: Times are set in epoch units
        $delete_UserProfile_Query = "DELETE from `#__user_profiles` where user_id =" . $db->quote($userId) . " AND 'profile_key' !='edulevel' AND profile_key !='gender' AND profile_key !='hispanic' AND profile_key !='organization' AND profile_key !='orgtype' AND profile_key !='race' AND profile_key !='reason'";
        $this->runUpdateOrDeleteQuery($delete_UserProfile_Query);

        $insert_UserProfileWithStatus_Query = "INSERT INTO `#__user_profiles` (`user_id`, `profile_key`, `profile_value`) values (?, 'deletion', 'marked')";
        $this->runInsertQuery($insert_UserProfileWithStatus_Query, $insert_userId_Vars);

        $update_SupportTicketsByEmail_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_SupportTicketsByEmail_Query);

        $update_SupportTicketsByLogin_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where login=" . $db->quote($userName);;
        $this->runUpdateOrDeleteQuery($update_SupportTicketsByLogin_Query);

        $delete_SessionGeo_Query = "DELETE from `#__session_geo` where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_SessionGeo_Query);

        $delete_Session_Query = "DELETE from `#__session` where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_Session_Query);

        $delete_SessionById_Query = "DELETE from `#__session` where userid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_SessionById_Query);

        $delete_ProfileCompletionAward_Query = "DELETE from `#__profile_completion_awards` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_ProfileCompletionAward_Query);

        $update_NewsletterMailingRecipientActions_Query = "UPDATE `#__newsletter_mailing_recipient_actions` set email='',ip='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingRecipientActions_Query);

        $update_NewsletterMailingRecipients_Query = "UPDATE `#__newsletter_mailing_recipients` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingRecipients_Query);

        $update_NewsletterMailingListEmails_Query = "UPDATE `#__newsletter_mailinglist_emails` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingListEmails_Query);

        $update_NewsletterMailingListUnsubscribes_Query = "UPDATE `#__newsletter_mailinglist_unsubscribes` set email='' where email=" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_NewsletterMailingListUnsubscribes_Query);

        $delete_Messages_Query = "DELETE from `#__messages` where user_id_from=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_Messages_Query);

        $update_MediaTrackingDetailed_Query = "UPDATE `#__media_tracking_detailed` set ip_address='' where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_MediaTrackingDetailed_Query);

        $update_MediaTracking_Query = "UPDATE `#__media_tracking` set ip_address='' where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_MediaTracking_Query);

        $delete_JobsSeeker_Query = "DELETE from `#__jobs_seekers` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsSeeker_Query);

        $delete_JobsResume_Query = "DELETE from `#__jobs_resumes` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsResume_Query);

        $delete_JobsApplications_Query = "DELETE from `#__jobs_applications` where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_JobsApplications_Query);

        $delete_Feedback_Query = "DELETE from `#__feedback` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_Feedback_Query);

        $update_EventRegistration_Query = "UPDATE `#__event_registration` set name='', email='', phone='', address='', city='', zip='', username=" . $db->quote($anonUserName) . " where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_EventRegistration_Query);

        $delete_CartSavedAddresses_Query = "DELETE from `#__cart_saved_addresses` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_CartSavedAddresses_Query);

        $delete_BlogEntries_Query = "DELETE from `#__blog_entries` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_BlogEntries_Query);

        $delete_AuthLinkData_Query = "DELETE from `#__auth_link_data` where link_id=" . $db->quote($userLinkId);
        $this->runUpdateOrDeleteQuery($delete_AuthLinkData_Query);

        $delete_AuthLink_Query = "DELETE from `#__auth_link` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_AuthLink_Query);

        $update_Job_Query = "UPDATE job set username=" . $db->quote($anonUserName) . " where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_Job_Query);

        $update_FilePerm_Query = "UPDATE fileperm set fileuser=" . $db->quote($anonUserName) . " where fileuser=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_FilePerm_Query);

        $update_ViewPerm_Query = "UPDATE viewperm set viewuser=" . $db->quote($anonUserName) . " where viewuser=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_ViewPerm_Query);

        $update_ViewLog_Query = "UPDATE viewlog set username=" . $db->quote($anonUserName) . ", remoteip='', remotehost='' where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_ViewLog_Query);

        $update_SessionLog_Query = "UPDATE sessionlog set username=" . $db->quote($anonUserName) . ", remoteip='', remotehost='' where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_SessionLog_Query);

        $update_Session_Query = "UPDATE session set username=" . $db->quote($anonUserName) . ", remoteip='' where username=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($update_Session_Query);

        $delete_XGroupMember_Query = "DELETE from `#__xgroups_members` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XGroupMember_Query);

        $delete_XProfilesBio_Query = "DELETE from `#__xprofiles_bio` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesBio_Query);

        $delete_XProfilesAddress_Query = "DELETE from `#__xprofiles_address` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesAddress_Query);

        $delete_XMessage_Query = "DELETE from `#__xmessage` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XMessage_Query);

        $delete_XProfilesAdmin_Query = "DELETE from `#__xprofiles_admin` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesAdmin_Query);

        $delete_XProfilesDisability_Query = "DELETE from `#__xprofiles_disability` where uidNumber=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesDisability_Query);

        $delete_XProfilesTokens_Query = "DELETE from `#__xprofiles_tokens` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_XProfilesTokens_Query);

        $delete_WishAttachment_Query = "DELETE from `#__wish_attachments` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_WishAttachment_Query);

        $delete_WikiAttachment_Query = "DELETE from `#__wiki_attachments` where created_by=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_WikiAttachment_Query);

        $delete_UsersQuotasLogByUserName_Query = "DELETE from `#__users_quotas_log` where name=" . $db->quote($userName);
        $this->runUpdateOrDeleteQuery($delete_UsersQuotasLogByUserName_Query);

        $delete_UsersQuotasLogByActorId_Query = "DELETE from `#__users_quotas_log` where actor_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersQuotasLogByActorId_Query);

        $delete_UsersLogAuth_Query = "DELETE from `#__users_log_auth` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersLogAuth_Query);

        $delete_UsersPassword_Query = "DELETE from `#__users_password` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersPassword_Query);

        $delete_UsersPasswordHistory_Query = "DELETE from `#__users_password_history` where user_id=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($delete_UsersPasswordHistory_Query);

        $update_UsersPointsSubscription_Query = "UPDATE `#__users_points_subscriptions` set contact=" . $db->quote($anonUserName) . " where uid=" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_UsersPointsSubscription_Query);

        // ----------- UPDATES TO THE PROFILES AND USERS TABLE, and User Profiles Table  ----------
        $update_XProfilesByEmail_Query = "UPDATE `#__xprofiles` set name=" . $db->quote($anonUserName) . ", username=" . $db->quote($anonUserName) . ", userPassword=" . $db->quote($anonPassword) . ", url='', phone='', regHost='', regIP='', givenName=" . $db->quote($anonUserName) . ", middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email=" . $db->quote($anonUserName . "@example.com") . " where email =" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_XProfilesByEmail_Query);

        $update_XProfilesById_Query = "UPDATE `#__xprofiles` set name=" . $db->quote($anonUserName) . ", username=" . $db->quote($anonUserName) . ", userPassword=" . $db->quote($anonPassword) . ", url='', phone='', regHost='', regIP='', givenName=" . $db->quote($anonUserName) . ", middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email=" . $db->quote($anonUserName . "@example.com") . " where uidNumber =" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_XProfilesById_Query);

        $update_UsersByEmail_Query = "UPDATE `#__users` set name=" . $db->quote($anonUserName) . ", givenName=" . $db->quote($anonUserName) .", middleName='', surname='', username=" . $db->quote($anonUserName) . ", password=" .  $db->quote($anonPassword) . ", block='1', registerIP='', params='', homeDirectory='', email=" .  $db->quote($anonUserName . "@example.com") . " where email =" . $db->quote($userEmail);
        $this->runUpdateOrDeleteQuery($update_UsersByEmail_Query);

        $update_UsersById_Query = "UPDATE `#__users` set name=" . $db->quote($anonUserName) . ", givenName=" . $db->quote($anonUserName) .", middleName='', surname='', username=" . $db->quote($anonUserName) . ", password=" .  $db->quote($anonPassword) . ", block='1', registerIP='', params='', homeDirectory='', email=" .  $db->quote($anonUserName . "@example.com") . " where id =" . $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_UsersById_Query);

        $update_UserProfiles_Query = "UPDATE `#__user_profiles` SET profile_value='sanitized' WHERE user_id=". $db->quote($userId);
        $this->runUpdateOrDeleteQuery($update_UserProfiles_Query);

        // Missing?
        $delete_KbArticles_Query = "DELETE from `#__kb_articles` where created_by='" . $user_id . "'";
        $delete_Content_Query = "DELETE from `#__content` where created_by='" . $user_id . "'";
    }
}
