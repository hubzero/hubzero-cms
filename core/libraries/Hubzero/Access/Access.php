<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Access;

use Hubzero\Utility\Arr;
use SimpleXMLElement;
use App;

/**
 * Class that handles all access authorization routines.
 */
class Access
{
	/**
	 * Array of view levels
	 *
	 * @var  array
	 */
	protected static $viewLevels = array();

	/**
	 * Array of rules for the asset
	 *
	 * @var  array
	 */
	protected static $assetRules = array();

	/**
	 * Array of user groups.
	 *
	 * @var  array
	 */
	protected static $userGroups = array();

	/**
	 * Array of user group paths.
	 *
	 * @var  array
	 */
	protected static $userGroupPaths = array();

	/**
	 * Array of cached groups by user.
	 *
	 * @var  array
	 */
	protected static $groupsByUser = array();

	/**
	 * Method for clearing static caches.
	 *
	 * @return  void
	 */
	public static function clearStatics()
	{
		self::$viewLevels     = array();
		self::$assetRules     = array();
		self::$userGroups     = array();
		self::$userGroupPaths = array();
		self::$groupsByUser   = array();
	}

	/**
	 * Method to check if a user is authorised to perform an action, optionally on an asset.
	 *
	 * @param   integer  $userId  Id of the user for which to check authorisation.
	 * @param   string   $action  The name of the action to authorise.
	 * @param   mixed    $asset   Integer asset id or the name of the asset as a string.  Defaults to the global asset node.
	 * @return  boolean  True if authorised.
	 */
	public static function check($userId, $action, $asset = null)
	{
		// Sanitise inputs.
		$userId = (int) $userId;

		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$asset  = strtolower(preg_replace('#[\s\-]+#', '.', trim($asset)));

		// Default to the root asset node.
		if (empty($asset))
		{
			$asset = Asset::getRootId();
		}

		// Get the rules for the asset recursively to root if not already retrieved.
		if (empty(self::$assetRules[$asset]))
		{
			self::$assetRules[$asset] = self::getAssetRules($asset, true);
		}

		// Get all groups against which the user is mapped.
		$identities = self::getGroupsByUser($userId);
		array_unshift($identities, $userId * -1);

		return self::$assetRules[$asset]->allow($action, $identities);
	}

	/**
	 * Method to check if a group is authorised to perform an action, optionally on an asset.
	 *
	 * @param   integer  $groupId  The path to the group for which to check authorisation.
	 * @param   string   $action   The name of the action to authorise.
	 * @param   mixed    $asset    Integer asset id or the name of the asset as a string.  Defaults to the global asset node.
	 * @return  boolean  True if authorised.
	 */
	public static function checkGroup($groupId, $action, $asset = null)
	{
		// Sanitize inputs.
		$groupId = (int) $groupId;
		$action = strtolower(preg_replace('#[\s\-]+#', '.', trim($action)));
		$asset  = strtolower(preg_replace('#[\s\-]+#', '.', trim($asset)));

		// Get group path for group
		$groupPath = self::getGroupPath($groupId);

		// Default to the root asset node.
		if (empty($asset))
		{
			$rootId = Asset::getRootId();
		}

		// Get the rules for the asset recursively to root if not already retrieved.
		if (empty(self::$assetRules[$asset]))
		{
			self::$assetRules[$asset] = self::getAssetRules($asset, true);
		}

		return self::$assetRules[$asset]->allow($action, $groupPath);
	}

	/**
	 * Gets the parent groups that a leaf group belongs to in its branch back to the root of the tree
	 * (including the leaf group id).
	 *
	 * @param   mixed  $groupId  An integer or array of integers representing the identities to check.
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 */
	protected static function getGroupPath($groupId)
	{
		// Preload all groups
		if (empty(self::$userGroups))
		{
			self::$userGroups = array();

			$groups = Group::all()
				->order('lft', 'asc')
				->rows();
			foreach ($groups as $group)
			{
				self::$userGroups[$group->get('id')] = $group;
			}
		}

		// Make sure groupId is valid
		if (!array_key_exists($groupId, self::$userGroups))
		{
			return array();
		}

		// Get parent groups and leaf group
		if (!isset(self::$userGroupPaths[$groupId]))
		{
			self::$userGroupPaths[$groupId] = array();

			foreach (self::$userGroups as $group)
			{
				if ($group->get('lft') <= self::$userGroups[$groupId]->get('lft')
				 && $group->get('rgt') >= self::$userGroups[$groupId]->get('rgt'))
				{
					self::$userGroupPaths[$groupId][] = $group->get('id');
				}
			}
		}

		return self::$userGroupPaths[$groupId];
	}

	/**
	 * Method to return the Rules object for an asset.  The returned object can optionally hold
	 * only the rules explicitly set for the asset or the summation of all inherited rules from
	 * parent assets and explicit rules.
	 *
	 * @param   mixed    $asset      Integer asset id or the name of the asset as a string.
	 * @param   boolean  $recursive  True to return the rules object with inherited rules.
	 * @return  object   Rules object for the asset.
	 */
	public static function getAssetRules($asset, $recursive = false)
	{
		// Build the database query to get the rules for the asset.
		$model = Asset::blank();

		$db = App::get('db');
		$query = $db->getQuery()
			->select($recursive ? 'b.rules' : 'a.rules');

		$query->from($model->getTableName(), 'a');

		// If the asset identifier is numeric assume it is a primary key, else lookup by name.
		if (is_numeric($asset))
		{
			$query->whereEquals('a.id', (int) $asset);
		}
		else
		{
			$query->whereEquals('a.name', $asset);
		}

		// If we want the rules cascading up to the global asset node we need a self-join.
		if ($recursive)
		{
			$query->joinRaw($model->getTableName() . ' AS b', 'b.lft <= a.lft AND b.rgt >= a.rgt', 'left');
			$query->order('b.lft', 'asc');
		}

		$query->group($recursive ? 'b.id, b.rules, b.lft' : 'a.id, a.rules, a.lft');

		$db->setQuery($query->toString());
		$result = $db->loadColumn();

		//$result = $query->rows()->fieldsByKey('rules');

		// Get the root even if the asset is not found and in recursive mode
		if (empty($result) && $recursive)
		{
			$result = Asset::oneOrFail(Asset::getRootId());
			$result = array($result->get('rules'));
		}

		// Instantiate and return the Rules object for the asset rules.
		$rules = new Rules;
		$rules->mergeCollection($result);

		return $rules;
	}

	/**
	 * Method to return a list of user groups mapped to a user. The returned list can optionally hold
	 * only the groups explicitly mapped to the user or all groups both explicitly mapped and inherited
	 * by the user.
	 *
	 * @param   integer  $userId     Id of the user for which to get the list of groups.
	 * @param   boolean  $recursive  True to include inherited user groups.
	 * @return  array    List of user group ids to which the user is mapped.
	 */
	public static function getGroupsByUser($userId, $recursive = true)
	{
		// Creates a simple unique string for each parameter combination:
		$storeId = $userId . ':' . (int) $recursive;

		if (!isset(self::$groupsByUser[$storeId]))
		{
			// Guest user (if only the actually assigned group is requested)
			if (empty($userId) && !$recursive)
			{
				$result = array(\Component::params('com_members')->get('guest_usergroup', 1));
			}
			// Registered user and guest if all groups are requested
			else
			{
				$db = App::get('db');

				// Build the database query to get the rules for the asset.
				$query = $db->getQuery();
				$query->select($recursive ? 'b.id' : 'a.id');
				if (empty($userId))
				{
					$query->from('#__usergroups', 'a')
						->whereEquals('a.id', (int) \Component::params('com_members')->get('guest_usergroup', 1));
				}
				else
				{
					$query->from('#__user_usergroup_map', 'map')
						->whereEquals('map.user_id', (int) $userId)
						->join('#__usergroups AS a', 'a.id', 'map.group_id', 'left');
				}

				// If we want the rules cascading up to the global asset node we need a self-join.
				if ($recursive)
				{
					$query->joinRaw('#__usergroups AS b', 'b.lft <= a.lft AND b.rgt >= a.rgt', 'left');
				}

				// Execute the query and load the rules from the result.
				$db->setQuery($query->toString());
				$result = $db->loadColumn();

				// Clean up any NULL or duplicate values, just in case
				Arr::toInteger($result);

				if (empty($result))
				{
					$result = array('1');
				}
				else
				{
					$result = array_unique($result);
				}
			}

			self::$groupsByUser[$storeId] = $result;
		}

		return self::$groupsByUser[$storeId];
	}

	/**
	 * Method to return a list of user Ids contained in a Group
	 *
	 * @param   integer  $groupId    The group Id
	 * @param   boolean  $recursive  Recursively include all child groups (optional)
	 * @return  array
	 * @todo    This method should move somewhere else
	 */
	public static function getUsersByGroup($groupId, $recursive = false)
	{
		$test = $recursive ? '>=' : '=';

		// First find the users contained in the group
		$db = App::get('db');
		$query = $db->getQuery()
			->select('DISTINCT(user_id)')
			->from('#__usergroups', 'ug1')
			->joinRaw('#__usergroups AS ug2', 'ug2.lft' . $test . 'ug1.lft AND ug1.rgt' . $test . 'ug2.rgt', 'inner')
			->join('#__user_usergroup_map AS m', 'm.group_id', 'ug2.id', 'inner')
			->whereEquals('ug1.id', $groupId);

		$db->setQuery($query->toString());

		$result = $db->loadColumn();

		// Clean up any NULL values, just in case
		Arr::toInteger($result);

		return $result;
	}

	/**
	 * Method to return a list of view levels for which the user is authorised.
	 *
	 * @param   integer  $userId  Id of the user for which to get the list of authorised view levels.
	 * @return  array    List of view levels for which the user is authorised.
	 */
	public static function getAuthorisedViewLevels($userId)
	{
		// Get all groups that the user is mapped to recursively.
		$groups = self::getGroupsByUser($userId);

		// Only load the view levels once.
		if (empty(self::$viewLevels))
		{
			// Build the view levels array.
			$levels = Viewlevel::all()
				->rows();

			foreach ($levels as $level)
			{
				self::$viewLevels[$level->get('id')] = (array) json_decode($level->get('rules'));
			}
		}

		// Initialise the authorised array.
		$authorised = array(1);

		// Find the authorised levels.
		foreach (self::$viewLevels as $level => $rule)
		{
			foreach ($rule as $id)
			{
				if (($id < 0) && (($id * -1) == $userId))
				{
					$authorised[] = $level;
					break;
				}
				// Check to see if the group is mapped to the level.
				elseif (($id >= 0) && in_array($id, $groups))
				{
					$authorised[] = $level;
					break;
				}
			}
		}

		return $authorised;
	}

	/**
	 * Method to return a list of actions from a file for which permissions can be set.
	 *
	 * @param   string  $file   The path to the XML file.
	 * @param   string  $xpath  An optional xpath to search for the fields.
	 * @return  boolean|array   False if case of error or the list of actions available.
	 */
	public static function getActionsFromFile($file, $xpath = "/access/section[@name='component']/")
	{
		if (!is_file($file))
		{
			// If unable to find the file return false.
			return false;
		}

		// Else return the actions from the xml.
		return self::getActionsFromData(self::getXml($file, true), $xpath);
	}

	/**
	 * Method to return a list of actions from a string or from an xml for which permissions can be set.
	 *
	 * @param   string|SimpleXMLElement  $data   The XML string or an XML element.
	 * @param   string                   $xpath  An optional xpath to search for the fields.
	 * @return  boolean|array   False if case of error or the list of actions available.
	 */
	public static function getActionsFromData($data, $xpath = "/access/section[@name='component']/")
	{
		// If the data to load isn't already an XML element or string return false.
		if (!($data instanceof SimpleXMLElement) && !is_string($data))
		{
			return false;
		}

		// Attempt to load the XML if a string.
		if (is_string($data))
		{
			$data = self::getXml($data, false);

			// Make sure the XML loaded correctly.
			if (!$data)
			{
				return false;
			}
		}

		// Initialise the actions array
		$actions = array();

		// Get the elements from the xpath
		$elements = $data->xpath($xpath . 'action[@name][@title][@description]');

		// If there some elements, analyse them
		if (!empty($elements))
		{
			foreach ($elements as $action)
			{
				// Add the action to the actions array
				$actions[] = (object) array(
					'name'        => (string) $action['name'],
					'title'       => (string) $action['title'],
					'description' => (string) $action['description']
				);
			}
		}

		// Finally return the actions array
		return $actions;
	}

	/**
	 * Reads an XML file or string.
	 *
	 * @param   string   $data    Full path and file name.
	 * @param   boolean  $isFile  true to load a file or false to load a string.
	 * @return  mixed    SimpleXMLElement on success or false on error.
	 * @todo    This may go in a separate class - error reporting may be improved.
	 */
	public static function getXml($data, $isFile = true)
	{
		// Disable libxml errors and allow to fetch error information as needed
		libxml_use_internal_errors(true);

		if ($isFile)
		{
			// Try to load the XML file
			$xml = simplexml_load_file($data);
		}
		else
		{
			// Try to load the XML string
			$xml = simplexml_load_string($data);
		}

		return $xml;
	}
}
