<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die;

// Set toolbar items for the page
Toolbar::title(Lang::txt('COM_CPANEL'), 'cpanel.png');
Toolbar::help('cpanel');

echo JHtml::_('sliders.start', 'panel-sliders', array('useCookie' => '1'));

foreach ($this->modules as $module)
{
	$output = JModuleHelper::renderModule($module);

	$params = new JRegistry;
	$params->loadString($module->params);

	if ($params->get('automatic_title', '0') == '0')
	{
		echo JHtml::_('sliders.panel', $module->title, 'cpanel-panel-' . $module->name);
	}
	elseif (method_exists('mod' . $module->name . 'Helper', 'getTitle'))
	{
		echo JHtml::_('sliders.panel', call_user_func_array(array('mod' . $module->name . 'Helper', 'getTitle'), array($params)), 'cpanel-panel-' . $module->name);
	}
	else
	{
		echo JHtml::_('sliders.panel', Lang::txt('MOD_' . $module->name . '_TITLE'), 'cpanel-panel-' . $module->name);
	}
	echo $output;
}

echo JHtml::_('sliders.end');