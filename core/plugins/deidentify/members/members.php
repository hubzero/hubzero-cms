<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Members Plugin class for Deidentification
 */
class plgDeidentifyMembers extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	public function onUserDeidentify($user_id) {
		echo "com_members: deidentify $user_id<br>";

		$db = \App::get('db');

        // PURPOSE: Find username, id, email from jos_users table
        $query = "SELECT id, username, email, password FROM `#__users` WHERE id='" . $user_id . "';";
        $db->setQuery($query);
        $userObjRows = $db->loadObjectList();

        // json_encode: returns a string containing the JSON representation from the mySQL -> json_decode: Returns the value encoded in json in appropriate PHP type
        // Get the userId, userEmail, userName
        $userString = json_encode($userObjRows, true);
        $userJsonObj = json_decode($userString, true);
        $userId = $userJsonObj[0]['id'];
        $userEmail = $userJsonObj[0]['email'];
        $userName = $userJsonObj[0]['username'];

        // Creating New Credentials
        $anonUserId = "anon_user_" . $userId;
        $anonPassword = "anon_password_" . $userId;
        $anonUserName = "anon_username_" . $userId;

        // PURPOSE: Find auth link id from jos_auth_link table
        $select_AuthLink_Query = "SELECT id, user_id FROM `#__auth_link` WHERE user_id='" . $user_id . "';";
        $db->setQuery($select_AuthLink_Query);
        $authLinkRows = $db->loadObjectList();
        $authLinkString = json_encode($authLinkRows, true);
        $authLinkJsonObj = json_decode($authLinkString, true);
        $userLinkId = $authLinkJsonObj[0]['id'];

		// ---- CREATE A FUNCTION THAT WILL RETURN -----

        // ======= Sanitation Queries // deletes, updates, inserts =======
        // NOTE: Times are set in epoch units
        $delete_UserProfile_Query = "DELETE from 'jos_user_profiles' where 'user_id' ='" . $user_id .
            "' AND 'profile_key' !='edulevel' AND profile_key !='gender' AND profile_key !='hispanic' AND profile_key !='organization' AND profile_key !='orgtype' AND profile_key !='race' AND profile_key !='reason'";
        $insert_UserProfileWithStatus_Query = "INSERT INTO 'jos_user_profiles' ('user_id', 'profile_key', 'profile_value') values ('" . $user_id . "', 'deletion', 'marked')";
        $update_SupportTicketsByEmail_Query = "UPDATE 'jos_support_tickets' set login='',ip='', email='', hostname='', name='' where email='" . $userEmail ."'";
        $update_SupportTicketsByLogin_Query = "UPDATE 'jos_support_tickets' set login='',ip='', email='', hostname='', name='' where login='" . $userName ."'";
        $delete_SessionGeo_Query = "DELETE from 'jos_session_geo' where username='" . $userName. "'";
        $delete_Session_Query = "DELETE from 'jos_session' where username='" . $userName. "'";
        $delete_ProfileCompletionAward_Query = "DELETE from 'jos_profile_completion_awards' where user_id='" . $user_id. "'";
        $update_NewsletterMailingRecipientActions_Query = "UPDATE 'jos_newsletter_mailing_recipient_actions' set email='',ip='' where email='" . $userEmail . "'";
        $update_NewsletterMailingRecipients_Query = "UPDATE 'jos_newsletter_mailing_recipients' set email='' where email='" . $userEmail . "'";
        $update_NewsletterMailingListEmails_Query = "UPDATE 'jos_newsletter_mailinglist_emails' set email='' where email='" . $userEmail . "'";
        $update_NewsletterMailingListUnsubscribes_Query = "UPDATE 'jos_newsletter_mailinglist_unsubscribes' set email='' where email='" . $userEmail . "'";
        $delete_Messages_Query = "DELETE from 'jos_messages' where user_id_from='" . $user_id . "'";
        $update_MediaTrackingDetailed_Query = "UPDATE 'jos_media_tracking_detailed' set ip_address='' where user_id='" . $user_id . "'";
        $update_MediaTracking_Query = "UPDATE 'jos_media_tracking' set ip_address='' where user_id='" . $user_id . "'";
        $delete_JobsSeeker_Query = "DELETE from 'jos_jobs_seekers' where uid='" . $user_id . "'";
        $delete_JobsResume_Query = "DELETE from 'jos_jobs_resumes' where uid='" . $user_id . "'";
        $delete_JobsApplications_Query = "DELETE from 'jos_jobs_applications' where uid='" . $user_id . "'";
        $delete_Feedback_Query = "DELETE from 'jos_feedback' where user_id='" . $user_id . "'";
        $update_EventRegistration_Query = "UPDATE 'jos_event_registration' set name='', email='', phone='', address='', city='', zip='', username='" . $anonUserName . "' where username='" . $userName . "'";
        $delete_CartSavedAddresses_Query = "DELETE from 'jos_cart_saved_addresses' where uidNumber='" . $user_id . "'";
        $delete_BlogEntries_Query = "DELETE from 'jos_blog_entries' where created_by='" . $user_id . "'";
        $delete_AuthLinkData_Query = "DELETE from 'jos_auth_link_data' where link_id='" . $userLinkId . "'";
        $delete_AuthLink_Query = "DELETE from 'jos_auth_link' where user_id='" . $user_id . "'";
        $update_Job_Query = "UPDATE 'job' set 'username'='" . $anonUserName . "' where 'username' ='" . $userName . "'";
        $update_FilePerm_Query = "UPDATE 'fileperm' set 'fileuser'='" . $anonUserName . "' where 'fileuser'='" . $userName . "'";
        $update_ViewPerm_Query = "UPDATE 'viewperm' set 'viewuser'='" . $anonUserName . "' where 'viewuser'='" . $userName . "'";
        $update_ViewLog_Query = "UPDATE 'viewlog' set 'username'='" . $anonUserName . "', 'remoteip'='', 'remotehost'='' where 'username'='" . $userName . "'";
        $update_SessionLog_Query = "UPDATE 'sessionlog' set 'username'='" . $anonUserName . "', 'remoteip'='', 'remotehost'='' where 'username'='" . $userName . "'";
        $update_Session_Query = "UPDATE 'session' set 'username'='" . $anonUserName . "', 'remoteip'='' where 'username'='" . $userName . "'";
        $delete_XGroupMember_Query = "DELETE from 'jos_xgroups_members' where uidNumber='" . $user_id . "'";
        $delete_XProfilesBio_Query = "DELETE from 'jos_xprofiles_bio' where uidNumber='" . $user_id . "'";
        $delete_XProfilesAddress_Query = "DELETE from 'jos_xprofiles_address' where uidNumber='" . $user_id . "'";
        $delete_XMessage_Query = "DELETE from 'jos_xmessage' where created_by='" . $user_id . "'";
        $delete_XProfilesAdmin_Query = "DELETE from 'jos_xprofiles_admin' where uidNumber='" . $user_id . "'";
        $delete_XProfilesDisability_Query = "DELETE from 'jos_xprofiles_disability' where uidNumber='" . $user_id . "'";
        $delete_XProfilesTokens_Query = "DELETE from 'jos_xprofiles_tokens' where user_id='" . $user_id . "'";

//        -- consider deleting these files:\n {{wish_attachment_files.stdout}}"
        $delete_WishAttachment_Query = "DELETE from 'jos_wish_attachments' where created_by='" . $user_id . "'";

//        -- consider deleting these files under /var/www/<hubname>/app/site/wiki/<page_id>/<filename>:\n {{wiki_attachment_files.stdout}}"
        $delete_WikiAttachment_Query = "DELETE from 'jos_wiki_attachments' where created_by='" . $user_id . "'";

        $delete_UsersQuotasLogByUserName_Query = "DELETE from 'jos_users_quotas_log' where name='" . $userName . "'";
        $delete_UsersQuotasLogByActorId_Query = "DELETE from 'jos_users_quotas_log' where actor_id='" . $user_id . "'";

        $delete_UsersLogAuth_Query = "DELETE from 'jos_users_log_auth' where user_id='" . $user_id . "'";
        $delete_UsersPassword_Query = "DELETE from 'jos_users_password' where user_id='" . $user_id . "'";
        $delete_UsersPasswordHistory_Query = "DELETE from 'jos_users_password_history' where user_id='" . $user_id . "'";

        $update_UsersPointsSubscription_Query = "UPDATE 'jos_users_points_subscriptions' set 'contact'='" . $anonUserName . "' where 'uid'='" . $user_id . "'";

        // ----------- UPDATES TO THE PROFILES AND USERS TABLE, and User Profiles Table  ----------
        $update_XProfilesByEmail_Query = "UPDATE 'jos_xprofiles' set name='" . $anonUserName . "', username='" . $anonUserName .
            "', userPassword='" . $anonPassword . "', url='', phone='', regHost='', regIP='', givenName='" . $anonUserName .
            "', middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email='" . $anonUserName . "@example.com' where email ='" . $userEmail . "'";

        $update_XProfilesById_Query = "UPDATE 'jos_xprofiles' set name='" . $anonUserName . "', username='" . $anonUserName .
            "', userPassword='" . $anonPassword . "', url='', phone='', regHost='', regIP='', givenName='" . $anonUserName .
            "', middleName='', surname='', picture='', public=0, params='', note='', orcid='', homeDirectory='/home/anonymous', email='" . $anonUserName . "@example.com' where uidNumber ='" . $userId . "'";

        $update_UsersByEmail_Query = "UPDATE 'jos_users' set name='" . $anonUserName . "', givenName= '" . $anonUserName ."', middleName='', surname='', username='" . $anonUserName .
            "', password='" . $anonPassword . "', block='1', registerIP='', params='', homeDirectory='', email='" .  $anonUserName . "@example.com' where email ='" . $userEmail . "'";

        $update_UsersById_Query = "UPDATE 'jos_users' set name='" . $anonUserName . "', givenName= '" . $anonUserName ."', middleName='', surname='', username='" . $anonUserName .
            "', password='" . $anonPassword . "', block='1', registerIP='', params='', homeDirectory='', email='" .  $anonUserName . "@example.com' where id ='" . $userId . "'";

        $update_UserProfiles_Query = "UPDATE jos_user_profiles SET profile_value='sanitized' WHERE user_id='" . $userId ."'";


        // Missing?
        $delete_KbArticles_Query = "DELETE from 'jos_kb_articles' where created_by='" . $user_id . "'";
        $delete_Content_Query = "DELETE from 'jos_content' where created_by='" . $user_id . "'";
	}
}