<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Users\Site;

use Hubzero\Component\Router\Base;
use Lang;
use App;

/**
 * Routing class for the component
 */
class Router extends Base
{
	/**
	 * Build the route for the component.
	 *
	 * @param   array  &$query  An array of URL arguments
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 */
	public function build(&$query)
	{
		// Declare static variables.
		static $items;
		static $default;
		static $registration;
		static $profile;
		static $link;
		static $login;
		static $logout;
		static $remind;
		static $resend;
		static $reset;

		// Initialise variables.
		$segments = array();

		// Get the relevant menu items if not loaded.
		if (empty($items))
		{
			// Get all relevant menu items.
			$menu  = App::get('menu');
			$items = $menu->getItems('component', 'com_users');

			// Build an array of serialized query strings to menu item id mappings.
			for ($i = 0, $n = count($items); $i < $n; $i++)
			{
				// Check to see if we have found the resend menu item.
				if (empty($resend) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'resend'))
				{
					$resend = $items[$i]->id;
				}

				// Check to see if we have found the reset menu item.
				if (empty($reset) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'reset'))
				{
					$reset = $items[$i]->id;
				}

				// Check to see if we have found the remind menu item.
				if (empty($remind) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'remind'))
				{
					$remind = $items[$i]->id;
				}

				// Check to see if we have found the link menu item.
				if (empty($link) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'link'))
				{
					$link = $items[$i]->id;
				}

				// Check to see if we have found the login menu item.
				if (empty($login) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'login'))
				{
					$login = $items[$i]->id;
				}

				// Check to see if we have found the logout menu item.
				if (empty($logout) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'logout'))
				{
					$logout = $items[$i]->id;
				}

				// Check to see if we have found the registration menu item.
				//if (empty($registration) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'registration'))
				//{
				//	$registration = $items[$i]->id;
				//}

				// Check to see if we have found the profile menu item.
				//if (empty($profile) && !empty($items[$i]->query['view']) && ($items[$i]->query['view'] == 'profile'))
				//{
				//$profile = $items[$i]->id;
				//}
			}

			// Set the default menu item to use for com_users if possible.
			if ($profile)
			{
				$default = $profile;
			}
			elseif ($registration)
			{
				$default = $registration;
			}
			elseif ($login)
			{
				$default = $login;
			}
		}

		if (!empty($query['view']))
		{
			switch ($query['view'])
			{
				case 'reset':
					if ($query['Itemid'] = $reset)
					{
						unset($query['view']);
					}
					else
					{
						$query['Itemid'] = $default;
					}
					break;

				case 'resend':
					if ($query['Itemid'] = $resend)
					{
						unset($query['view']);
					}
					else
					{
						$query['Itemid'] = $default;
					}
					break;

				case 'remind':
					if ($query['Itemid'] = $remind)
					{
						unset($query['view']);
					}
					else
					{
						$query['Itemid'] = $default;
					}
					break;

				case 'endsinglesignon':
					break;

				case 'link':
					break;

				case 'logout':
					if ($query['Itemid'] = $logout)
					{
						unset($query['view']);
					}
					else
					{
						$query['Itemid'] = $default;
					}
					break;

				default:
				case 'login':
					if ($query['Itemid'] = $login)
					{
						unset($query['view']);
					}
					else
					{
						$query['Itemid'] = $default;
					}
					break;

				//case 'registration':
				//	if ($query['Itemid'] = $registration)
				//	{
				//		unset ($query['view']);
				//	}
				//	else
				//	{
				//		$query['Itemid'] = $default;
				//	}
				//	break;

				//default:
				//case 'profile':
				//	if (!empty($query['view']))
				//	{
				//		$segments[] = $query['view'];
				//	}
				//	unset ($query['view']);
				//	if ($query['Itemid'] = $profile)
				//	{
				//		unset ($query['view']);
				//	}
				//	else
				//	{
				//		$query['Itemid'] = $default;
				//	}

					// Only append the user id if not "me".
				//	$user = User::getInstance();
				//	if (!empty($query['user_id']) && ($query['user_id'] != $user->id))
				//	{
				//		$segments[] = $query['user_id'];
				//	}
				//	unset ($query['user_id']);

				//	break;
			}
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 * @return  array  The URL attributes to be used by the application.
	 */
	public function parse(&$segments)
	{
		// Initialise variables.
		$vars = array();

		// Only run routine if there are segments to parse.
		if (count($segments) < 1)
		{
			return;
		}

		// Get the package from the route segments.
		$userId = array_pop($segments);

		// Make routes such as "/users/remind" and "/users/reset" work
		if ($userId == 'remind' || $userId == "reset")
		{
			$vars['view'] = $userId;
			return $vars;
		}

		if (!is_numeric($userId))
		{
			$vars['view'] = 'login';
			return $vars;
		}

		if (is_numeric($userId))
		{
			// Get the package id from the packages table by alias.
			$db = App::get('db');
			$db->setQuery(
				'SELECT '.$db->quoteName('id') .
				' FROM '.$db->quoteName('#__users') .
				' WHERE '.$db->quoteName('id').' = '.(int) $userId
			);
			$userId = $db->loadResult();
		}

		// Set the package id if present.
		if ($userId)
		{
			// Set the package id.
			$vars['user_id'] = (int)$userId;

			// Set the view to package if not already set.
			if (empty($vars['view']))
			{
				$vars['view'] = 'login';
			}
		}
		else
		{
			App::abort(404, Lang::txt('JGLOBAL_RESOURCE_NOT_FOUND'));
		}

		return $vars;
	}
}
