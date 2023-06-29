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

    public function runDeleteQuery($query) {
        $db = \App::get('db');
        $db->setQuery($query);
        return $db->query();
    }


	public function onUserDeidentify($user_id) {
        $db = \App::get('db');

        // PURPOSE: Find username, id, email from jos_users table
        $select_UsersById_Query = "SELECT id, username, email, password FROM `#__users` WHERE id='" . $user_id . "';";
        $userJsonObj = $this->runSelectQuery($select_UsersById_Query);

        $userId = ""; $userEmail = ""; $userName = ""; $userLinkId = "";

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
        $delete_UserProfile_Query = "DELETE from `#__user_profiles` where user_id =" . $db->quote($user_id) . " AND 'profile_key' !='edulevel' AND profile_key !='gender' AND profile_key !='hispanic' AND profile_key !='organization' AND profile_key !='orgtype' AND profile_key !='race' AND profile_key !='reason'";
        $this->runDeleteQuery($delete_UserProfile_Query);

        $insert_UserProfileWithStatus_Query = "INSERT INTO `#__user_profiles` (`user_id`, `profile_key`, `profile_value`) values (?, 'deletion', 'marked')";
        $this->runInsertQuery($insert_UserProfileWithStatus_Query, $insert_userId_Vars);
//
//        $update_SupportTicketsByEmail_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where email='" . $userEmail ."'";
//        $update_SupportTicketsByEmail_Response = $this->runQueryStatement($update_SupportTicketsByEmail_Query);
//        print_r($update_SupportTicketsByEmail_Response);
//
//        $update_SupportTicketsByLogin_Query = "UPDATE `#__support_tickets` set login='',ip='', email='', hostname='', name='' where login='" . $userName ."'";
//        $update_SupportTicketsByLogin_Response = $this->runQueryStatement($update_SupportTicketsByLogin_Query);
//        print_r($update_SupportTicketsByLogin_Response);
//
//        $delete_SessionGeo_Query = "DELETE from `#__session_geo` where username='" . $userName. "'";
//        $delete_SessionGeo_Response = $this->runQueryStatement($delete_SessionGeo_Query);
//        print_r($delete_SessionGeo_Response);
//
//        $delete_Session_Query = "DELETE from `#__session` where username='" . $userName. "'";
//        $delete_Session_Response = $this->runQueryStatement($delete_Session_Query);
//        print_r($delete_Session_Response);
//
//        $delete_ProfileCompletionAward_Query = "DELETE from `#__profile_completion_awards` where user_id='" . $user_id. "'";
//        $delete_ProfileCompletionAward_Response = $this->runQueryStatement($delete_ProfileCompletionAward_Query);
//        print_r($delete_ProfileCompletionAward_Response);
//
//        $update_NewsletterMailingRecipientActions_Query = "UPDATE `#__newsletter_mailing_recipient_actions` set email='',ip='' where email='" . $userEmail . "'";
//        $update_NewsletterMailingRecipientActions_Response = $this->runQueryStatement($update_NewsletterMailingRecipientActions_Query);
//        print_r($update_NewsletterMailingRecipientActions_Response);
//
//        $update_NewsletterMailingRecipients_Query = "UPDATE `#__newsletter_mailing_recipients` set email='' where email='" . $userEmail . "'";
//        $updateNewsletterMailingRecipientsResponse = $this->runQueryStatement($update_NewsletterMailingRecipients_Query);
//        print_r($updateNewsletterMailingRecipientsResponse);
//
//        $update_NewsletterMailingListEmails_Query = "UPDATE `#__newsletter_mailinglist_emails` set email='' where email='" . $userEmail . "'";
//        $update_NewsletterMailingListEmails_Response = $this->runQueryStatement($update_NewsletterMailingListEmails_Query);
//        print_r($update_NewsletterMailingListEmails_Response);
//
//        $update_NewsletterMailingListUnsubscribes_Query = "UPDATE `#__newsletter_mailinglist_unsubscribes` set email='' where email='" . $userEmail . "'";
//        $update_NewsletterMailingListUnsubscribes_Response = $this->runQueryStatement($update_NewsletterMailingListUnsubscribes_Query);
//        print_r($update_NewsletterMailingListUnsubscribes_Response);
//
//        $delete_Messages_Query = "DELETE from `#__messages` where user_id_from='" . $user_id . "'";
//        $delete_Messages_Response = $this->runQueryStatement($delete_Messages_Query);
//        print_r($delete_Messages_Response);
//
//        $update_MediaTrackingDetailed_Query = "UPDATE `#__media_tracking_detailed` set ip_address='' where user_id='" . $user_id . "'";
//        $update_MediaTrackingDetailed_Response = $this->runQueryStatement($update_MediaTrackingDetailed_Query);
//        print_r($update_MediaTrackingDetailed_Response);
//
//        $update_MediaTracking_Query = "UPDATE `#__media_tracking` set ip_address='' where user_id='" . $user_id . "'";
//        $update_MediaTracking_Response = $this->runQueryStatement($update_MediaTracking_Query);
//        print_r($update_MediaTracking_Response);
//
//        $delete_JobsSeeker_Query = "DELETE from `#__jobs_seekers` where uid='" . $user_id . "'";
//        $delete_JobsSeeker_Response = $this->runQueryStatement($delete_JobsSeeker_Query);
//        print_r($delete_JobsSeeker_Response);
//
//        $delete_JobsResume_Query = "DELETE from `#__jobs_resumes` where uid='" . $user_id . "'";
//        $delete_JobsResume_Response = $this->runQueryStatement($delete_JobsResume_Query);
//        print_r($delete_JobsResume_Response);
//
//        $delete_JobsApplications_Query = "DELETE from `#__jobs_applications` where uid='" . $user_id . "'";
//        $delete_JobsApplications_Response = $this->runQueryStatement($delete_JobsApplications_Query);
//        print_r($delete_JobsApplications_Response);
//
//        $delete_Feedback_Query = "DELETE from `#__feedback` where user_id='" . $user_id . "'";
//        $delete_Feedback_Response = $this->runQueryStatement($delete_Feedback_Query);
//        print_r($delete_Feedback_Response);
//
//        $update_EventRegistration_Query = "UPDATE `#__event_registration` set name='', email='', phone='', address='', city='', zip='', username='" . $anonUserName . "' where username='" . $userName . "'";
//        $update_EventRegistration_Response = $this->runQueryStatement($update_EventRegistration_Query);
//        print_r($update_EventRegistration_Response);
//
//        $delete_CartSavedAddresses_Query = "DELETE from `#__cart_saved_addresses` where uidNumber='" . $user_id . "'";
//        $delete_CartSavedAddresses_Response = $this->runQueryStatement($delete_CartSavedAddresses_Query);
//        print_r($delete_CartSavedAddresses_Response);
//
//        $delete_BlogEntries_Query = "DELETE from `#__blog_entries` where created_by='" . $user_id . "'";
//        $delete_BlogEntries_Response = $this->runQueryStatement($delete_BlogEntries_Query);
//        print_r($delete_BlogEntries_Response);
//
//        $delete_AuthLinkData_Query = "DELETE from `#__auth_link_data` where link_id='" . $userLinkId . "'";
//        $delete_AuthLinkData_Response = $this->runQueryStatement($delete_AuthLinkData_Query);
//        print_r($delete_AuthLinkData_Response);
//
//        $delete_AuthLink_Query = "DELETE from `#__auth_link` where user_id='" . $user_id . "'";
//        $delete_AuthLink_Response = $this->runQueryStatement($delete_AuthLink_Query);
//        print_r($delete_AuthLink_Response);
//
//        $update_Job_Query = "UPDATE job set 'username'='" . $anonUserName . "' where 'username' ='" . $userName . "'";
//        $update_Job_Response = $this->runQueryStatement($update_Job_Query);
//        print_r($update_Job_Response);
//
//        $update_FilePerm_Query = "UPDATE fileperm set 'fileuser'='" . $anonUserName . "' where 'fileuser'='" . $userName . "'";
//        $update_FilePerm_Response = $this->runQueryStatement($update_FilePerm_Query);
//        print_r($update_FilePerm_Response);
//
//        $update_ViewPerm_Query = "UPDATE viewperm set 'viewuser'='" . $anonUserName . "' where 'viewuser'='" . $userName . "'";
//        $update_ViewPerm_Response = $this->runQueryStatement($update_ViewPerm_Query);
//        print_r($update_ViewPerm_Response);
//
//        $update_ViewLog_Query = "UPDATE viewlog set 'username'='" . $anonUserName . "', 'remoteip'='', 'remotehost'='' where 'username'='" . $userName . "'";
//        $update_ViewLog_Response = $this->runQueryStatement($update_ViewLog_Query);
//        print_r($update_ViewLog_Response);
//
//        $update_SessionLog_Query = "UPDATE sessionlog set 'username'='" . $anonUserName . "', 'remoteip'='', 'remotehost'='' where 'username'='" . $userName . "'";
//        $update_SessionLog_Response = $this->runQueryStatement($update_SessionLog_Query);
//        print_r($update_SessionLog_Response);
//
//        $update_Session_Query = "UPDATE session set 'username'='" . $anonUserName . "', 'remoteip'='' where 'username'='" . $userName . "'";
//        $update_Session_Response = $this->runQueryStatement($update_Session_Query);
//        print_r($update_Session_Response);
//
//        $delete_XGroupMember_Query = "DELETE from `#__xgroups_members` where uidNumber='" . $user_id . "'";
//        $delete_XGroupMember_Response = $this->runQueryStatement($delete_XGroupMember_Query);
//        print_r($delete_XGroupMember_Response);
//
//        $delete_XProfilesBio_Query = "DELETE from `#__xprofiles_bio` where uidNumber='" . $user_id . "'";
//        $delete_XProfilesBio_Response = $this->runQueryStatement($delete_XProfilesBio_Query);
//        print_r($delete_XProfilesBio_Response);
//
//        $delete_XProfilesAddress_Query = "DELETE from `#__xprofiles_address` where uidNumber='" . $user_id . "'";
//        $delete_XProfilesAddress_Response = $this->runQueryStatement($delete_XProfilesAddress_Query);
//        print_r($delete_XProfilesAddress_Response);
//
//        $delete_XMessage_Query = "DELETE from `#__xmessage` where created_by='" . $user_id . "'";
//        $delete_XMessage_Response = $this->runQueryStatement($delete_XMessage_Query);
//        print_r($delete_XMessage_Response);
//
//        $delete_XProfilesAdmin_Query = "DELETE from `#__xprofiles_admin` where uidNumber='" . $user_id . "'";
//        $delete_XProfilesAdmin_Response = $this->runQueryStatement($delete_XProfilesAdmin_Query);
//        print_r($delete_XProfilesAdmin_Response);
//
//        $delete_XProfilesDisability_Query = "DELETE from `#__xprofiles_disability` where uidNumber='" . $user_id . "'";
//        $delete_XProfilesDisability_Response = $this->runQueryStatement($delete_XProfilesDisability_Query);
//        print_r($delete_XProfilesDisability_Response);
//
//        $delete_XProfilesTokens_Query = "DELETE from `#__xprofiles_tokens` where user_id='" . $user_id . "'";
//        $delete_XProfilesTokens_Response = $this->runQueryStatement($delete_XProfilesTokens_Query);
//        print_r($delete_XProfilesTokens_Response);
//
////        -- consider deleting these files:\n {{wish_attachment_files.stdout}}"
//        $delete_WishAttachment_Query = "DELETE from `#__wish_attachments` where created_by='" . $user_id . "'";
//
////        -- consider deleting these files under /var/www/<hubname>/app/site/wiki/<page_id>/<filename>:\n {{wiki_attachment_files.stdout}}"
//        $delete_WikiAttachment_Query = "DELETE from `#__wiki_attachments` where created_by='" . $user_id . "'";
//
//        $delete_UsersQuotasLogByUserName_Query = "DELETE from `#__users_quotas_log` where name='" . $userName . "'";
//        $delete_UsersQuotasLogByUserName_Response = $this->runQueryStatement($delete_UsersQuotasLogByUserName_Query);
//        print_r($delete_UsersQuotasLogByUserName_Response);
//
//        $delete_UsersQuotasLogByActorId_Query = "DELETE from `#__users_quotas_log` where actor_id='" . $user_id . "'";
//        $delete_UsersQuotasLogByActorId_Response = $this->runQueryStatement($delete_UsersQuotasLogByActorId_Query);
//        print_r($delete_UsersQuotasLogByActorId_Response);
//
//        $delete_UsersLogAuth_Query = "DELETE from `#__users_log_auth` where user_id='" . $user_id . "'";
//        $delete_UsersLogAuth_Response = $this->runQueryStatement($delete_UsersLogAuth_Query);
//        print_r($delete_UsersLogAuth_Response);
//
//        $delete_UsersPassword_Query = "DELETE from `#__users_password` where user_id='" . $user_id . "'";
//        $delete_UsersPassword_Response = $this->runQueryStatement($delete_UsersPassword_Query);
//        print_r($delete_UsersPassword_Response);
//
//        $delete_UsersPasswordHistory_Query = "DELETE from `#__users_password_history` where user_id='" . $user_id . "'";
//        $delete_UsersPasswordHistory_Response = $this->runQueryStatement($delete_UsersPasswordHistory_Query);
//        print_r($delete_UsersPasswordHistory_Response);
//
//        $update_UsersPointsSubscription_Query = "UPDATE `#__users_points_subscriptions` set 'contact'='" . $anonUserName . "' where 'uid'='" . $user_id . "'";
//        $update_UsersPointsSubscription_Response = $this->runQueryStatement($update_UsersPointsSubscription_Query);
//        print_r($update_UsersPointsSubscription_Response);
//
//        // ----------- UPDATES TO THE PROFILES AND USERS TABLE, and User Profiles Table  ----------
//        $update_XProfilesByEmail_Query = "UPDATE `#__xprofiles` set name='" . $anonUserName . "', username='" . $anonUserName . "', userPassword='" . $anonPassword . "', url='', phone='', regHost='', regIP='', givenName='" . $anonUserName . "', middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email='" . $anonUserName . "@example.com' where email ='" . $userEmail . "'";
//        $update_XProfilesByEmail_Response = $this->runQueryStatement($update_XProfilesByEmail_Query);
//        print_r($update_XProfilesByEmail_Response);
//
//        $update_XProfilesById_Query = "UPDATE `#__xprofiles` set name='" . $anonUserName . "', username='" . $anonUserName . "', userPassword='" . $anonPassword . "', url='', phone='', regHost='', regIP='', givenName='" . $anonUserName . "', middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email='" . $anonUserName . "@example.com' where uidNumber ='" . $userId . "'";
//        $update_XProfilesById_Response = $this->runQueryStatement($update_XProfilesById_Query);
//        print_r($update_XProfilesById_Response);
//
//        $update_UsersByEmail_Query = "UPDATE `#__users` set name='" . $anonUserName . "', givenName= '" . $anonUserName ."', middleName='', surname='', username='" . $anonUserName . "', password='" . $anonPassword . "', block='1', registerIP='', params='', homeDirectory='', email='" .  $anonUserName . "@example.com' where email ='" . $userEmail . "'";
//        $update_UsersByEmail_Response = $this->runQueryStatement($update_UsersByEmail_Query);
//        print_r($update_UsersByEmail_Response);
//
//        $update_UsersById_Query = "UPDATE `#__users` set name='" . $anonUserName . "', givenName= '" . $anonUserName ."', middleName='', surname='', username='" . $anonUserName . "', password='" . $anonPassword . "', block='1', registerIP='', params='', homeDirectory='', email='" .  $anonUserName . "@example.com' where id ='" . $userId . "'";
//        $update_UsersById_Response = $this->runQueryStatement($update_UsersById_Query);
//        print_r($update_UsersById_Response);
//
//        $update_UserProfiles_Query = "UPDATE `#__user_profiles` SET profile_value='sanitized' WHERE user_id='" . $userId ."'";
//        $update_UserProfiles_Response = $this->runQueryStatement($update_UserProfiles_Query);
//        print_r($update_UserProfiles_Response);
//
//        // Missing?
//        $delete_KbArticles_Query = "DELETE from `#__kb_articles` where created_by='" . $user_id . "'";
//        $delete_Content_Query = "DELETE from `#__content` where created_by='" . $user_id . "'";
    }
}
