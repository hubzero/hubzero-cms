<?php
/**
 * @version		
 * @package		Joomdle
 * @subpackage	Content
 * @copyright	Copyright (C) 2008 - 2010 Antonio Duran Terres
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.user.helper');
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joomdle'.DS.'helpers'.DS.'content.php');

/**
 * Content Component Query Helper
 *
 * @static
 * @package		Joomdle
 * @since 1.5
 */
class JoomdleHelperParents
{

	function getUnassignedCourses ()
	{
		$user = & JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$db           =& JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = '$id' and num > 0";

		$db->setQuery($sql);
		$courses = $db->loadObjectList();

		$i = 0;
		if (!$courses)
			return array();

		foreach ($courses as $course)
		{
			$course_info = JoomdleHelperContent::getCourseInfo ($course->course_id);

			$c[$i]['id'] = $course->course_id;
			$c[$i]['num'] = $course->num;
			$c[$i]['name'] = $course_info['fullname'];

			$i++;
		}

		return $c;
	}

	function getChildren ()
	{
		$user = & JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$db           =& JFactory::getDBO();

		$sql = "SELECT * from #__users" .
			" WHERE params LIKE '%parent_id=$id%'";

		$db->setQuery($sql);
		$users = $db->loadObjectList();

		if (!$users)
			return array ();

		$j = 0;
		foreach ($users as $child)
		{
			$c[$j]['id'] = $child->id;
			$c[$j]['name'] = $child->name;
			$courses = JoomdleHelperContent::getMyCourses ($child->username);
			$i = 0;
			if ((is_array ($courses)) && (count ($courses)))
			{
				foreach ($courses as $course)
				{
					$user_courses[$i] = $course['id'];
					$i++;
				}
			}
			else $user_courses = array ();
			$c[$j]['courses'] = $user_courses;
			$j++;
		}

		return $c;
	}

	function childrenSelect ($row_id = 1)
	{
		 $children = JoomdleHelperParents::getChildren ();
		 foreach ($children as $child)
		 {
			 $options[] = JHTML::_('select.option', $child['id'], $child['name']);

		 }
		 echo JHTML::_('select.genericlist', $options, 'children'.'['.$row_id.']', 'multiple=multiple', 'value', 'text'); //, $value, $control_name.$name );
	}


	function childrenCheckbox ($user_id, $course_id, $disabled)
	{
		//echo '<input type="checkbox" name="children_'.$user_id.'['.$course_id.']" value="'.$user_id.'">';
		//echo '<input type="checkbox" name="children_'.$user_id.'['.$course_id.']['.$user_id.']" value="'.$user_id.'">';
		//echo '<input type="checkbox" name="children_'.$user_id.'_'.$course_id.'" value="'.$user_id.'">';
		//echo '<input type="checkbox" name="children_'.$user_id.'_'.$course_id.'" value="1"'; //.$user_id.'">';
		echo '<input type="checkbox" name="children['.$course_id.'][]" value="'.$user_id.'"';
		if ($disabled)
			echo " disabled";
		echo '>';
	}


	function childrenCheckboxes ($course_id)
	{
		$children = JoomdleHelperParents::getChildren ();
		foreach ($children as $child)
		{
			if (in_array ($course_id, $child['courses']))
				$disabled = true;
			else
				$disabled = false;

			JoomdleHelperParents::childrenCheckbox ($child['id'], $course_id, $disabled);
			echo $child['name'];
			if ($disabled)
				echo  " ".JText::_( 'CJ ALREADY ENROLED' );
			echo "<br>";
		}
	}


	function assingment_available ($course_id, $assingment)
	{
		$user = & JFactory::getUser();
                $id = $user->get('id');
                $username = $user->get('username');

		$num = count ($assingment);

		$db           =& JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = " . $db->Quote($id) ." and num >= " . $db->Quote($num) .
			" AND course_id = " . $db->Quote($course_id);

		$db->setQuery($sql);
		$courses = $db->loadObjectList();

		if (!$courses)
			return false;

		return true;
	}

	function check_assign_availability ($assingments)
	{
		foreach ($assingments as $course_id => $a)
		{
			$available = JoomdleHelperParents::assingment_available($course_id, $a);
			if (!$available)
				return false;
		}
		return true;
	}

	function assign_courses ($assingments)
	{
		foreach ($assingments as $course_id => $a)
		{
			JoomdleHelperParents::assign_course($course_id, $a);
		}
	}

	function assign_course ($course_id, $assingment)
	{
		foreach ($assingment as $user_id)
		{
			$user = & JFactory::getUser($user_id);
			$username = $user->get('username');
			JoomdleHelperContent::enrolUser($username, $course_id);
			 /* Send confirmation email */
			JoomdleHelperShop::send_confirmation_email ($user->email, $course_id);
		}
		JoomdleHelperParents::update_purchase ($course_id, $assingment);
	}

	function update_purchase ($course_id, $assingment)
	{
		$user = & JFactory::getUser();
                $id = $user->get('id');

		$db           =& JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = ". $db->Quote($id) . " and course_id = " . $db->Quote($course_id);

		$db->setQuery($sql);
		$pc = $db->loadObject();

		$a->user_id = $id;
		$a->course_id = $course_id;

		if ($pc)
		{
			$a->id = $pc->id;
			$a->num = $pc->num - count ($assingment);
			/* Update row */
			$db->updateObject ('#__joomdle_purchased_courses', $a, 'id');
		}
	}

	function purchase_course ($username, $course_id, $num)
	{
		$user = & JFactory::getUser($username);
                $id = $user->get('id');

		$db           =& JFactory::getDBO();

		$sql = "SELECT * from #__joomdle_purchased_courses" .
			" WHERE user_id = ". $db->Quote($id) . " and course_id = " . $db->Quote($course_id);


		$db->setQuery($sql);
		$pc = $db->loadObject();

		$a->user_id = $id;
		$a->course_id = $course_id;

		if ($pc)
		{
			$a->id = $pc->id;
			$a->num = $num + $pc->num;
			/* Update row */
			$db->updateObject ('#__joomdle_purchased_courses', $a, 'id');
		}
		else
		{
			$a->num = $num;
			/* Insert row */
			$db->insertObject ('#__joomdle_purchased_courses', $a);
		}
	}

	function sync_parents_from_moodle ($users_ids)
	{
		foreach ($users_ids as $id)
		{
			$user =& JFactory::getUser($id);
			$parents = JoomdleHelperContent::call_method ('get_parents', $user->username);
			foreach ($parents as $parent)
			{
				$parent =& JFactory::getUser($parent['username']);
				$user->setParam('u'.$parent->id.'_parent_id', $parent->id);
				$user->save();
			}
		}
	}

}
