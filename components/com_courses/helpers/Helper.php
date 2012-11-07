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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_Course_Invite_Email'
 * 
 * Long description (if any) ...
 */
class Hubzero_Course_Helper
{
	
	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function niceidformat( $course_id )
	{
		while(strlen($course_id) < 5)
		{
			$course_id = 0 . $course_id;
		}
		return $course_id;
	}

	public static function getPopularCourses($limit=0)
	{
		//database object
		$database =& JFactory::getDBO();
		
		//query
		$sql = "SELECT g.id, g.alias, g.title, g.blurb, 
				(SELECT COUNT(*) FROM #__courses_offering_members AS gm WHERE gm.course_id=g.id) AS members
				FROM #__courses AS g 
				WHERE g.type=1
				AND g.state=1
				AND g.privacy=0
				ORDER BY members DESC";
		
		//do we want to limit return
		if($limit > 0)
		{
			$sql .= "  LIMIT {$limit}";
		}		
		
		//execute query and return result
		$database->setQuery( $sql );
		if(!$database->getError())
		{
			return $database->loadObjectList();
		}
	}
	
	
	public static function getCoursesMatchingTagString( $usertags, $usercourses )
	{
		//database object
		$database =& JFactory::getDBO();
		
		//
		$gt = new CoursesTags( $database );
	
		//turn users tag string into array
		$mytags = ($usertags != "") ? array_map("trim", explode(",", $usertags)) : array();
				
		//users courses
		$mycourses = array();
		if (is_array($usercourses))
		{
			foreach ($usercourses as $ug)
			{
				$mycourses[] = $ug->id;
			}
		}
		
		//query the databse for all published, type "HUB" courses
		$sql = "SELECT g.id, g.alias, g.title, g.blurb 
				FROM #__courses AS g
				WHERE g.type=1
				AND g.state=1";
		$database->setQuery( $sql );
		
		//get all courses
		$courses = $database->loadObjectList();
		
		//loop through each course and see if there is a tag match
		foreach($courses as $k => $course)
		{
			//get the courses tags
			$course->tags = $gt->get_tag_string( $course->id );
			$course->tags = array_map("trim", explode(",", $course->tags));
			
			//get common tags
			$course->matches = array_intersect($mytags, $course->tags);
			
			//remove tags from the course object since its no longer needed
			unset($course->tags);
			
			//if we dont have a match remove course from return results
			//or if we are already a member of the course remove from return results
			if(count($course->matches) < 1 || in_array($course->id, $mycourses))
			{
				unset($courses[$k]);
			}
		}
		
		return $courses;
	}
	
	
	public function listCourses( $name="", $config, $courses=array(), $num_columns=2, $display_logos=true, $display_private_description=false, $description_char_limit=150 )
	{
		//user object
		$user =& JFactory::getUser();
		
		//database object
		$database =& JFactory::getDBO();
		
		//check to see if we have any courses to show
		if(!$courses)
		{
			return "<p class=\"info\">".JText::sprintf('COM_COURSES_NO_'.str_replace(" ", "_",strtoupper($name)), $user->get("id"))."</p>";
		}
		
		//var to hold html
		$html = "";
		
		//var to hold count
		$count = 0;
		
		//import wiki parser
		ximport('Hubzero_Wiki_Parser');
		$p =& Hubzero_Wiki_Parser::getInstance();
		
		//loop through each course
		foreach($courses as $course)
		{
			//get the Hubzero Course Object
			$hg = CoursesCourse::getInstance( $course->id );
			
			//
			$gt = new CoursesTags( $database );
			
			//var to hold course description
			$description = "";
			
			//build the wiki config
			$wikiconfig = array(
				'option'   => $this->option,
				'scope'    => $hg->alias.DS.'wiki',
				'pagename' => 'course',
				'pageid'   => $hg->id,
				'filepath' => $config->get('uploadpath'),
				'domain'   => $hg->alias
			);
			
			//get the column were on
			switch($count)
			{
				case 0:		$cls = "first";		break;
				case 1: 	$cls = "second";	break;
				case 2:		$cls = "third";		break;
				case 3:		$cls = "fourth";	break;
			}
			
			//how many columns are we showing
			switch($num_columns)
			{
				case 2:		$columns = "two";	break;
				case 3:		$columns = "three";	break;
				case 4:		$columns = "four";	break;
			}
			
			//if we want to display private description and if we have a private description
			if($display_private_description && $hg->private_desc)
			{
				$description = $p->parse( "\n".stripslashes($hg->private_desc), $wikiconfig, true, true );
			}
			elseif($hg->public_desc)
			{
				$description = $p->parse( "\n".stripslashes($hg->public_desc), $wikiconfig, true, true );
			}
			
			//are we a course manager
			$isManager = (in_array($user->get("id"), $hg->get("managers"))) ? true : false;
			
			//if we have a description then strip tags, remove links, and shorten 
			if($description != "")
			{
				$description = strip_tags($description);
				$UrlPtrn  = "[^=\"\'](https?:|mailto:|ftp:|gopher:|news:|file:)" . "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
				$description = preg_replace("/$UrlPtrn/", '', $description);
			}
			else
			{
				$description = '<em>No course description available.</em>';
			}
			
			//shorten description
			$gdescription = substr($description, 0, $description_char_limit);
			$gdescription .= (strlen($description) > $description_char_limit && $description_char_limit != 0) ? "&hellip;" : "";
			
			//get the course logo
			if($hg->logo)
			{
				$logo = $config->get('uploadpath') . DS . $hg->id . DS . $hg->logo;
			}
			else 
			{
				$logo = "/components/com_courses/assets/img/course_default_logo.png";
			}
			
			//build the html
			$html .= "<div class=\"{$columns} columns {$cls} no-top-border\">";
				$html .= "<div class=\"course-list\">";
					if($display_logos)
					{
						$html .= "<div class=\"logo\"><img src=\"{$logo}\" alt=\"{$hg->title}\" /></div>";
						$d_cls = "-w-logo";
					}
					else
					{
						$d_cls = "";
					}
					$html .= "<div class=\"details{$d_cls}\">";
						$html .= "<h3><a href=\"/courses/{$course->alias}\">{$hg->title}</a></h3>";
						if($gdescription)
						{
							$html .= "<p>{$gdescription}</p>";
						}
						if($isManager)
						{
							$html .= "<span class=\"status manager\">Manager</span>";
						}
						if(isset($course->matches))
						{
							$html .= "<ol class=\"tags\">";
								foreach($course->matches as $t)
								{
									$html .= "<li><a href=\"/tags/".$gt->normalize_tag($t)."\">{$t}</a></li>";
								}
							$html .= "</ol>";
						}
					$html .= "</div>";
				$html .= "</div>";
			$html .= "</div>";
			
			//increment counter
			$count++;
			
			//move to next line depending on num columns
			if( ($cls == "second" && $columns == "two") || ($cls == "third" && $columns == "three") || ($cls == "fourth" && $columns == "four") ) 
			{
				$count = 0;
				$html .= "<br class=\"clear\" /><hr />";	
			}
		}
		
		return $html;
	}
	
	public function convertInviteEmails($email, $user_id)
	{
		// @FIXME: Should wrap this up in a nice transaction to handle partial failures and
		// race conditions.
	
		if (empty($email) || empty($user_id))
		{
			return false;
		}
		
		$db = JFactory::getDBO();
		
		$sql = 'SELECT id FROM #__courses_inviteemails WHERE email=' . $db->Quote($email) . ';';
		
		$db->setQuery($sql);
		
		$result = $db->loadResultArray();
		
		if ($result === false)
		{
			return false;
		}
		
		if (empty($result))
		{
			return true;
		}
		
		foreach($result as $r)
		{
			$values .= "($r,$user_id),";
			$gids   .= "$r,";
		}
		
		$values = rtrim($values,',');
		$gids = rtrim($gids,',');
		
		$sql = 'INSERT INTO #__courses_invitees (id,uidNumber) VALUES ' . $values . ';';

		$db->setQuery($sql);
		
		$result = $db->query();
		
		if (!$result)
		{
			return false;
		}
		
		$sql = 'DELETE FROM #__courses_inviteemails WHERE email=' . $db->Quote($email) . ' AND id IN (' . $gids . ');';
		
		$db->setQuery($sql);
		
		$result = $db->query();
		
		if (!$result)
		{
			return false;
		}
		
		return true;
	}
	
	
	//-----
	
	public static function displayCourseContent($sections, $cats, $active_tab)
	{
		//echo "<pre>";
		//print_r($sections);
		//echo "</pre>";
		for($i=0,$n=count($cats); $i < $n; $i++)
		{
			if($active_tab == $cats[$i]['name'])
			{
				return $sections[$i]['html'];
			}
		}
	}
	
	//-----
	
	public static function displayCourseMenu($course, $instance, $sections, $cats, $access_levels, $course_pages, $active_tab)
	{
		//instantiate objects
		$juser =& JFactory::getUser();
		
		//variable to hold course menu html
		$course_menu = "";
		
		//loop through each category and build menu item
		foreach($cats as $k => $cat)
		{
			//do we want to show category in menu?
			if($cat['display_menu_tab'])
			{
				//active menu item
				$li_cls = ($active_tab == $cat['name']) ? 'active' : '';
				
				//menu name & title
				$active = $cat['name'];
				$title = $cat['title'];
				$cls = $cat['name'];
				
				//get the menu items access level
				$access = $access_levels[$cat['name']];
				
				//menu link
				$link = JRoute::_('index.php?option=com_courses&gid='.$course->get("alias").'&instance=' . $instance->alias . '&active='.$active);
				
				//Are we on the overview tab with sub course pages?
				if($cat['name'] == 'overview' && count($course_pages) > 0)
				{
					$true_active_tab = JRequest::getVar('active', 'overview');
					$li_cls = ($true_active_tab != $active_tab) ? '' : $li_cls;
					
					if(($access == 'registered' && $juser->get('guest')) || ($access == 'members' && !in_array($juser->get("id"), $course->get('members'))))
					{
						$menu_item  = "<li class=\"protected course-overview-tab\"><span class=\"overview\">Overview</span>";
					}
					else
					{
						$menu_item  = "<li class=\"{$li_cls} course-overview-tab\">";
						$menu_item .= "<a class=\"overview\" title=\"{$course->get('description')}'s Overview Page\" href=\"{$link}\">Overview</a>";
					} 
					
					$menu_item .= "<ul class=\"\">";
					
					foreach($course_pages as $page)
					{
						//page access settings
						$page_access = ($page['privacy'] == 'default') ? $access : $page['privacy'];
						
						//page vars
						$title = $page['title'];
						$cls = ($true_active_tab == $page['url']) ? 'active' : '';
						$link = JRoute::_('index.php?option=com_courses&gid='.$course->get("alias").'&active='.$page['url']);
						
						//page menu item
						if(($page_access == 'registered' && $juser->get('guest')) || ($page_access == 'members' && !in_array($juser->get("id"), $course->get('members'))))
						{
							$menu_item .= "<li class=\"protected\"><span class=\"page\">{$title}</span></li>";
						}
						else
						{
							$menu_item .= "<li class=\"{$cls}\">";
							$menu_item .= "<a href=\"{$link}\" class=\"page\" title=\"{$course->get('description')}'s {$title} Page\">{$title}</a>";
							$menu_item .= "</li>";
						}
					}
					
					$menu_item .= "</ul>";
					$menu_item .= "</li>";
				}
				else
				{
					if($access == 'nobody')
					{
						$menu_item = '';
					}
					elseif($access == 'members' && !in_array($juser->get("id"), $course->get('members'))) 
					{
						$menu_item  = "<li class=\"protected members-only course-{$cls}-tab\" title=\"This page is restricted to course members only!\">";
						$menu_item .= "<span class=\"{$cls}\">{$title}</span>";
						$menu_item .= "</li>";
					}
					elseif($access == 'registered' && $juser->get('guest'))
					{
						$menu_item  = "<li class=\"protected registered-only course-{$cls}-tab\" title=\"This page is restricted to registered hub users only!\">";
						$menu_item .= "<span class=\"{$cls}\">{$title}</span>";
						$menu_item .= "</li>";
					}
					else
					{
						//menu item meta data vars
						$metadata = (isset($sections[$k]['metadata'])) ? $sections[$k]['metadata'] : array();
						$meta_count = (isset($metadata['count']) && $metadata['count'] != '') ? $metadata['count'] : '';
						$meta_alert = (isset($metadata['alert']) && $metadata['alert'] != '') ? $metadata['alert'] : '';

						//create menu item
						$menu_item  = "<li class=\"{$li_cls} course-{$cls}-tab\">";
						$menu_item .= "<a class=\"{$cls}\" title=\"{$course->get('description')}'s {$title} Page\" href=\"{$link}\">{$title}</a>";
						$menu_item .= "<span class=\"meta\">";
						if($meta_count)
						{
							$menu_item .= "<span class=\"count\">" . $meta_count . "</span>";
						}
						$menu_item .= "</span>";
						$menu_item .= $meta_alert;
						$menu_item .= "</li>";
					}
				} 
			
				//add menu item to variable holding entire menu
				$course_menu .= $menu_item;
			}
		}
		
		return $course_menu;
	}
	
	//----
	// New function for new courses (Chris)
	//----
	

	/**
	 * Short description for 'search_roles'
	 * Long description (if any) ...
	 *
	 * @param string $role Parameter description (if any) ...
	 * @return boolean Return description (if any) ...
	 */
	public static function search_roles($course, $role = '')
	{
		if ($role == '')
			return false;
		
		$db = & JFactory::getDBO();
		
		$query = "SELECT uidNumber FROM #__courses_roles as r, #__courses_member_roles as m WHERE r.id='" . $role . "' AND r.id=m.role AND r.id='" . $course->id . "'";
		
		$db->setQuery($query);
		
		$result = $db->loadResultArray();
		
		$result = array_intersect($result, $course->members);
		
		if (count($result) > 0)
		{
			return $result;
		}
	}
	
	//----
	// New function with new courses (Chris)
	//----

	/**
	 * Short description for 'getPluginAccess'
	 * Long description (if any) ...
	 *
	 * @param string $get_plugin Parameter description (if any) ...
	 * @return mixed Return description (if any) ...
	 */
	public static function getPluginAccess($course, $get_plugin = '')
	{
		// Get plugins
		JPluginHelper::importPlugin('courses');
		$dispatcher = & JDispatcher::getInstance();

		// Trigger the functions that return the areas we'll be using
		//then add overview to array
		$hub_course_plugins = $dispatcher->trigger('onCourseAreas', array());
		array_unshift($hub_course_plugins, array('name'=>'overview', 'title'=>'Overview', 'default_access'=>'anyone'));

		//array to store plugin preferences when after retrieved from db
		$active_course_plugins = array();

		//get the course plugin preferences
		//returns array of tabs and their access level (ex. [overview] => 'anyone', [messages] => 'registered')
		$course_plugins = $course->get('plugins');

		if ($course_plugins)
		{
			$course_plugins = explode(',', $course_plugins);

			foreach ($course_plugins as $plugin)
			{
				$temp = explode('=', trim($plugin));

				if ($temp[0])
				{
					$active_course_plugins[$temp[0]] = trim($temp[1]);
				}
			}
		}

		//array to store final course plugin preferences
		//array of acceptable access levels
		$course_plugin_access = array();
		$acceptable_levels = array('nobody', 'anyone', 'registered', 'members');

		//if we have already set some
		if ($active_course_plugins)
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				//if course defined access level is not an acceptable value or not set use default value that is set per plugin
				//else use course defined access level
				if (!isset($active_course_plugins[$hgp['name']]) || !in_array($active_course_plugins[$hgp['name']], $acceptable_levels))
				{
					$value = $hgp['default_access'];
				}
				else
				{
					$value = $active_course_plugins[$hgp['name']];
				}

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}
		else
		{
			//for each plugin that is active on the hub
			foreach ($hub_course_plugins as $hgp)
			{
				$value = $hgp['default_access'];

				//store final  access level in array of access levels
				$course_plugin_access[$hgp['name']] = $value;
			}
		}

		//if we wanted to return only a specific level return that otherwise return all access levels
		if ($get_plugin != '')
		{
			return $course_plugin_access[$get_plugin];
		}
		else
		{
			return $course_plugin_access;
		}
	}
}