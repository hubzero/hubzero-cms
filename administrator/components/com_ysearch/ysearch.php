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
defined('_JEXEC') or die( 'Restricted access' );
ini_set('display_errors', 1);
require_once JPATH_ROOT.'/components/com_ysearch/include.php';

JPluginHelper::importPlugin('ysearch');

$context = array();
if (array_key_exists('ysearch-task', $_POST))
	foreach (JApplication::triggerEvent('onYSearchTask'.$_POST['ysearch-task']) as $resp)
	{
		list($name, $html, $ctx) = $resp;
		echo $html;
		if (array_key_exists($name, $context))
			$context[$name] = array_merge($context[$name], $ctx);
		else
			$context[$name] = $ctx;
	}

JToolBarHelper::title(JText::_('Search'), 'search.png');

foreach (JApplication::triggerEvent('onYSearchAdministrate', array($context)) as $plugin)
{
	list($name, $html) = $plugin;
	echo '<h3>'.$name.'</h3>';
	echo $html;
}

