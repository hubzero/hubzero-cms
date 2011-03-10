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
class JoomdleHelperProfiletypes
{

	function getProfiletypes ($filter_type, $limitstart, $limit, $filter_order, $filter_order_Dir, $search)
	{
                $db           =& JFactory::getDBO();

		$wheres = array ();
		if ($filter_type)
		{
			/* kludge to use 0 as a value */
			if ($filter_type == -1)
				$filter_type = 0;
			$wheres[] = "create_on_moodle = ". $db->Quote($filter_type);
		}

		if ($search)
		{
			$wheres_search[] = "joomla_field = ". $db->Quote($search);
			$wheres_search[] = "moodle_field = ". $db->Quote($search);
			$wheres[] = "(name LIKE  ". $search .")";
		}

		$query = "SELECT * from #__xipt_profiletypes";
	//	$query = 'SELECT j.id, name, create_on_moodle 
	//		FROM #__xipt_profiletypes as x, #__joomdle_profiletypes as j 
	//		where x.id = j.profiletype_id';

		if(! empty($wheres)){
                   $query .= " AND ".implode(' AND ', $wheres);
                }

		$query .= " ORDER BY ".  $filter_order  ." ". $filter_order_Dir;

		if(! empty($limit)){
                   $query .= " LIMIT $limitstart, $limit";
                }

		$db->setQuery($query);
                $profiletypes = $db->loadObjectList();

		if (!$profiletypes)
			return NULL;

		foreach ($profiletypes as $profiletype)
		{
			$profiletype->published = JoomdleHelperProfiletypes::create_this_type ($profiletype->id);
			$profiletype->create_on_moodle = $profiletype->published;
			$m[] = $profiletype;
		}

		return $m;
	}

	/* Returns an array of profiles_id to bre created in moodle  */
	function get_profiletypes_to_create ()
	{
                $db           =& JFactory::getDBO();
		$query = "select profiletype_id from #__joomdle_profiletypes where create_on_moodle = 1";

                $db->setQuery($query);
                $ids = $db->loadResultArray();

		if (!$ids)
			return array();

		return $ids;
	}

	/* Checks if a types is to be created on moodle  */
	function create_this_type ($id)
	{
                $db           =& JFactory::getDBO();
		$query = "select create_on_moodle from #__joomdle_profiletypes where profiletype_id = ". $db->Quote($id);

                $db->setQuery($query);
                $create = $db->loadObject();

		if (!$create)
			return 0;

		return $create->create_on_moodle;
	}

	/* Sets a profile type to be created in moodle  */
	function create_on_moodle ($ids)
	{
                $db           =& JFactory::getDBO();

		foreach ($ids as $id)
		{
			$query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);

			$db->setQuery($query);
			$exists = $db->loadObject();

			if (!$exists)
			{
				//create
				$query = "insert into  #__joomdle_profiletypes (profiletype_id, create_on_moodle) VALUES ('$id', '1')";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
			else
			{
				//update
				$query = "update  #__joomdle_profiletypes set create_on_moodle=1 where profiletype_id =$id";
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
		}

	}

	/* Sets a profile type NOT to be created in moodle  */
	function dont_create_on_moodle ($ids)
	{
                $db           =& JFactory::getDBO();

		foreach ($ids as $id)
		{
			$query = "select * from #__joomdle_profiletypes where profiletype_id = " . $db->Quote($id);

			$db->setQuery($query);
			$exists = $db->loadObject();

			if (!$exists)
			{
				// do nothing
				continue;
			}
			else
			{
				//update
				$query = "update  #__joomdle_profiletypes set create_on_moodle=0 where profiletype_id = " . $db->Quote($id);
				$db->setQuery($query);
				if (!$db->query()) {
					return JError::raiseWarning( 500, $db->getError() );
				}
			}
		}

	}

}
