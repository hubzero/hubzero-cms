<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */


// no direct access
defined('_HZEXEC_') or die();

// Set toolbar items for the page
Toolbar::title(Lang::txt('COM_CPANEL'), 'cpanel.png');
Toolbar::help('cpanel');

echo Html::sliders('start', 'panel-sliders', array('useCookie' => '1'));

$modules = Module::byPosition('cpanel');

foreach ($modules as $module)
{
	$output = Module::render($module);

	$params = new Hubzero\Config\Registry($module->params);

	if ($params->get('automatic_title', '0') == '0')
	{
		echo Html::sliders('panel', $module->title, 'cpanel-panel-' . $module->name);
	}
	elseif (method_exists('mod' . $module->name . 'Helper', 'getTitle'))
	{
		echo Html::sliders('panel', call_user_func_array(array('mod' . $module->name . 'Helper', 'getTitle'), array($params)), 'cpanel-panel-' . $module->name);
	}
	else
	{
		echo Html::sliders('panel', Lang::txt('MOD_' . $module->name . '_TITLE'), 'cpanel-panel-' . $module->name);
	}
	echo $output;
}

echo Html::sliders('end');
