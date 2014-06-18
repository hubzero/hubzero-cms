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

function NewsletterBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['id']))
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `alias` FROM #__newsletters WHERE id=" . $database->quote( $query['id'] );
		$database->setQuery($sql);
		$campaign = $database->loadResult();
		$segments[] = strtolower(str_replace(" ", "", $campaign));
		unset($query['id']);
	}

	if (!empty($query['task']))
	{
		if (in_array($query['task'], array('subscribe','unsubscribe','resendconfirmation')))
		{
			$segments[] = $query['task'];
			unset($query['task']);
		}
	}

	return $segments;
}

function NewsletterParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
	{
    	return $vars;
	}

	if (isset($segments[0]))
	{
		$database = JFactory::getDBO();
		$sql = "SELECT `id` FROM #__newsletters WHERE alias=" . $database->quote( $segments[0] );
		$database->setQuery($sql);
		$campaignId = $database->loadResult();

		if ($campaignId)
		{
			$vars['id'] = $campaignId;
		}
		else
		{
			switch( $segments[0] )
			{
				case 'track':
					$vars['task'] = 'track';
					$vars['type'] = $segments[1];
					$vars['controller'] = 'mailing';
					break;
				case 'confirm':
				case 'remove':
				case 'subscribe':
				case 'dosubscribe':
				case 'unsubscribe':
				case 'dounsubscribe':
				case 'resendconfirmation':
					$vars['task'] = $segments[0];
					$vars['controller'] = 'mailinglist';
					break;
				default:
					$vars['task'] = $segments[0];
			}
		}
	}

	return $vars;
}

?>
