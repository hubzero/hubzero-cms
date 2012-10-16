<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    David Benham <dbenham@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Plugin');

/**
 * Courses Plugin class for forum entries
 */
class plgCoursesUserEnrollment extends Hubzero_Plugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}
                    
	public function onCourseUserEnrollment($gidNumber, $userid)
	{
		$xlog = &Hubzero_Factory::getLogger();
		$db = JFactory::getDBO();

		
		$course = new Hubzero_Course();
		$course->read( $gidNumber );
		$discussion_email_autosubscribe = $course->get('discussion_email_autosubscribe');

$xlog->logDebug('$discussion_email_autosubscribe' . $discussion_email_autosubscribe);	
		if (!$discussion_email_autosubscribe) 
			return;
		
		// see if they've already got something, they shouldn't, but you never know
		$query = "SELECT COUNT(userid) FROM #__courses_memberoption WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";   
		$db->setQuery($query);
		$count = $db->loadResult();
		
		if($count){
			$query = "UPDATE #__courses_memberoption SET optionvalue = 1 WHERE gidNumber=" . $gidNumber . " AND userid=" . $userid . " AND optionname='receive-forum-email'";   
			$db->setQuery($query);
			$db->query();
		}
		else{
			$query = "INSERT INTO #__courses_memberoption(gidNumber, userid, optionname, optionvalue) VALUES('" . $gidNumber . "', '" . $userid . "', 'receive-forum-email', '1')";
			$db->setQuery($query);
			$db->query();
		}
		
	}

}