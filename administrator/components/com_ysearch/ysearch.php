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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$option = 'com_ysearch';

// Authorization check
if (version_compare(JVERSION, '1.6', 'lt'))
{
	$jacl = JFactory::getACL();
	$jacl->addACL($option, 'manage', 'users', 'super administrator');
	$jacl->addACL($option, 'manage', 'users', 'administrator');
	$jacl->addACL($option, 'manage', 'users', 'manager');

	$user = JFactory::getUser();
	if (!$user->authorize($option, 'manage'))
	{
		$app = JFactory::getApplication();
		$app->redirect( 'index.php', JText::_('ALERTNOTAUTH') );
	}
}
else 
{
	if (!JFactory::getUser()->authorise('core.manage', $option)) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}
}

require_once JPATH_ROOT . DS . 'components' . DS . $option . DS . 'include.php';

JPluginHelper::importPlugin('ysearch');

$context = array();
if (array_key_exists('ysearch-task', $_POST))
{
	foreach (JApplication::triggerEvent('onYSearchTask' . $_POST['ysearch-task']) as $resp)
	{
		list($name, $html, $ctx) = $resp;
		echo $html;
		if (array_key_exists($name, $context))
		{
			$context[$name] = array_merge($context[$name], $ctx);
		}
		else
		{
			$context[$name] = $ctx;
		}
	}
}

JToolBarHelper::title(JText::_('Search'), 'search.png');

foreach (JApplication::triggerEvent('onYSearchAdministrate', array($context)) as $plugin)
{
	list($name, $html) = $plugin;
	echo '<h3>' . $name . '</h3>';
	echo $html;
}

