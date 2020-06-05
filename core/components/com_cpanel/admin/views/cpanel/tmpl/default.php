<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
