<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for 2011/12 content
 **/
class Migration20120101000006Core extends Base
{
	public function up()
	{
		// Component entries
		$this->addComponentEntry('Forum');
		$this->addComponentEntry('Register');
		$this->addComponentEntry('System');

		// @FIXME: Save params for geodb and ldap?

		// Plugins
		$this->addPluginEntry('groups', 'calendar');
		$this->addPluginEntry('groups', 'memberoptions');
		$this->addPluginEntry('groups', 'userenrollment');
		$this->addPluginEntry('support', 'captcha');
		$this->addPluginEntry('support', 'kb');
		$this->addPluginEntry('resources', 'about');
		$this->addPluginEntry('resources', 'abouttool');
		$this->addPluginEntry('resources', 'sponsors');
		$this->addPluginEntry('members', 'profile');
		$this->addPluginEntry('members', 'dashboard');
		$this->addPluginEntry('members', 'account');
		$this->addPluginEntry('ysearch', 'citations');
		$this->addPluginEntry('ysearch', 'forum');
		$this->addPluginEntry('tags', 'forum');
		$this->addPluginEntry('hubzero', 'wikieditorwykiwyg');
		$this->addPluginEntry('hubzero', 'comments');
		$this->addPluginEntry('hubzero', 'recaptcha');
		$this->addPluginEntry('system', 'jquery', 1, array(
			"jquery"             => "1",
			"jquerycdnpath"      => "\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/1.7.2\/jquery.min.js",
			"jqueryui"           => "1",
			"jqueryuicdnpath"    => "\/\/ajax.googleapis.com\/ajax\/libs\/jqueryui\/1.8.6\/jquery-ui.min.js",
			"jqueryuicss"        => "1",
			"jqueryuicsspath"    => "\/media\/system\/css\/jquery.ui.css",
			"jquerytools"        => "1",
			"jquerytoolscdnpath" => "http:\/\/cdn.jquerytools.org\/1.2.5\/all\/jquery.tools.min.js",
			"jqueryfb"           => "1",
			"jqueryfbcdnpath"    => "\/\/fancyapps.com\/fancybox\/",
			"jqueryfbcss"        => "1",
			"jqueryfbcsspath"    => "\/media\/system\/css\/jquery.fancybox.css",
			"activateSite"       => "1",
			"noconflictSite"     => "0",
			"activateAdmin"      => "0",
			"noconflictAdmin"    => "0"
		));
		$this->addPluginEntry('authentication', 'hubzero');
		$this->addPluginEntry('authentication', 'facebook');
		$this->addPluginEntry('authentication', 'google');
		$this->addPluginEntry('authentication', 'linkedin');
		$this->addPluginEntry('user', 'ldap');

		// Modules
		$this->installModule('search', 'search', array(
			'label' => 'Search',
			'width' => '20',
			'text'  => 'Search'
		));

		// Deletes
		$this->deleteComponentEntry('userpoints');
		$this->deleteComponentEntry('xpoll');
		$this->deleteComponentEntry('sef');
		$this->deleteComponentEntry('ldap');
		$this->deleteComponentEntry('geodb');
		$this->deleteComponentEntry('apc');
		$this->deleteComponentEntry('ximport');
		$this->deleteComponentEntry('myaccount');
		$this->deleteComponentEntry('xflash');
		$this->deleteComponentEntry('xsearch');
		$this->deleteComponentEntry('whois');
		$this->deleteComponentEntry('myhub');
		$this->deletePluginEntry('authentication', 'xauth');
		$this->deletePluginEntry('xauthentication');
		$this->deletePluginEntry('xsearch');
		$this->deletePluginEntry('xhub', 'xlibrary');
		$this->deletePluginEntry('user', 'breeze');
		$this->deleteModuleEntry('mod_myprofile');
		$this->disablePlugin('authentication', 'joomla');

		// Update faq text
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/change_password','/members/{{userid}}/changepassword')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/members/{{userid}}/changepassword','/members/myaccount/changepassword')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/mynanohub/account/','/members/{{userid}}/')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/members/{{userid}}/','/members/myaccount/')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/lostpassword','/login/reset')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/lostusername','/login/remind')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__faq` SET `fulltxt` = REPLACE(`fulltxt`,'/report_problems/','/feedback/report_problems')";
		$this->db->setQuery($query);
		$this->db->query();

		// Insert resource licenses
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc25-by-nc-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.\r\n\r\nNotice — For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page.','Creative Commons BY-NC-SA 2.5',6,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/2.5/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc25-by-nc-sa')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by-nc-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC-SA 3.0',7,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by-nc-sa')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.\r\n\r\nNotice — For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page.','Creative Commons',1,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-sa/2.5/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by-nc-nd','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nNo Derivative Works — You may not alter, transform, or build upon this work.\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC-ND 3.0',8,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc-nd/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by-nc-nd')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY 3.0',2,0,NULL,NULL,'http://creativecommons.org/licenses/by/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by-sa','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nShare Alike — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one.\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-SA 3.0',3,0,NULL,NULL,'http://creativecommons.org/licenses/by-sa/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by-sa')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by-nd','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto make commercial use of the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNo Derivative Works — You may not alter, transform, or build upon this work.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\nThe author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-ND 3.0',4,0,NULL,NULL,'http://creativecommons.org/licenses/by-nd/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by-nd')";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "INSERT INTO `#__resource_licenses` (`name`, `text`, `title`, `ordering`, `apps_only`, `main`, `icon`, `url`, `agreement`, `info`)
				  SELECT 'cc30-by-nc','You are free:\r\n\r\nto Share — to copy, distribute and transmit the work\r\nto Remix — to adapt the work\r\n\r\nUnder the following conditions:\r\n\r\nAttribution — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).\r\n\r\nNoncommercial — You may not use this work for commercial purposes.\r\n\r\nWith the understanding that:\r\n\r\nWaiver — Any of the above conditions can be waived if you get permission from the copyright holder.\r\n\r\nPublic Domain — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license.\r\n\r\nOther Rights — In no way are any of the following rights affected by the license:\r\n- Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations;\r\n- The author\'s moral rights;\r\n- Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights.','Creative Commons BY-NC 3.0',5,0,NULL,NULL,'http://creativecommons.org/licenses/by-nc/3.0/',0,NULL
				  FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `#__resource_licenses` WHERE `name` = 'cc30-by-nc')";
		$this->db->setQuery($query);
		$this->db->query();

		// Update timezones
		$query = "UPDATE `#__events` SET `time_zone` = -5 where `time_zone` = 'est'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -4 where `time_zone` = 'edt'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -6 where `time_zone` = 'cst'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -5 where `time_zone` = 'cdt'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -7 where `time_zone` = 'mst'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -6 where `time_zone` = 'mdt'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -8 where `time_zone` = 'pst'";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__events` SET `time_zone` = -7 where `time_zone` = 'pdt'";
		$this->db->setQuery($query);
		$this->db->query();

		// Initial population of users password table
		$query = "INSERT IGNORE INTO `#__users_password` (`user_id`,`passhash`) SELECT uidNumber, userPassword FROM #__xprofiles";
		$this->db->setQuery($query);
		$this->db->query();

		// Update support tickets to use new open field
		$query = "UPDATE `#__support_tickets` SET `open`=0 WHERE `status`=2";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__support_tickets` SET `status`=2, `open`=1 WHERE `status`=1";
		$this->db->setQuery($query);
		$this->db->query();
		$query = "UPDATE `#__support_tickets` SET `status`=1 WHERE (`owner` != '' AND `owner` IS NOT NULL) AND `open`=1 AND `status`=0";
		$this->db->setQuery($query);
		$this->db->query();

		// Change xpoll module entries to use poll module
		$query = "UPDATE `#__modules` SET `module`='mod_poll' WHERE `module`='mod_xpoll'";
		$this->db->setQuery($query);
		$this->db->query();

		// Change to use hub menu module
		$query = "UPDATE `#__modules` SET `module`='mod_hubmenu' WHERE `module`='mod_menu' AND `client_id`='1'";
		$this->db->setQuery($query);
		$this->db->query();

		// Update login redirect url
		$query = "UPDATE `#__menu` SET `params` = REPLACE(`params`,'login=/myhub','login=/members/myaccount/') WHERE `alias` = 'login'";
		$this->db->setQuery($query);
		$this->db->query();
	}
}