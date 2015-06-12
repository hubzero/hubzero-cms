<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing dated kb article on password changing
 **/
class Migration20131024182332Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$current  = "<p>We have an online form to <a href=\"/change_password\" title=\"Change password form\">change your password</a>.  ";
		$current .= "You can use the link included here, or you can find the same link on the page that you go to when you first ";
		$current .= "<a href=\"/login\">log in</a> to this site.  You\'ll also find the link on your ";
		$current .= "<a href=\"/mynanohub/account/\">My Account</a> page.</p>";

		$new  = "<p>We have multiple methods available to help you change your password. ";
		$new .= "If you can't remember your password, go to the <a href=\"/login/reset\">forgot password</a> page to reset it. ";
		$new .= "If you're already logged in, and simply wish to change your password, go to your ";
		$new .= "<a href=\"/members/myaccount/account\">account page</a> for a quick password change form.</p>";

		$query = "SELECT * FROM `#__faq` WHERE `alias` = 'pwchange'";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$sub1 = substr(stripslashes($r->fulltxt), 0, 254);
				$sub2 = substr($current, 0, 254);
				$distance = levenshtein($sub1, $sub2);

				if ($distance < 50)
				{
					$query = "UPDATE `#__faq` SET `fulltxt` = " . $this->db->quote($new) . " WHERE `id` = " . $this->db->quote($r->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}