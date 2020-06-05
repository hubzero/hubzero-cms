<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SEARCH') . ': ' . Lang::txt('COM_SEARCH_SITEMAP'), 'search.png');
Toolbar::preferences('com_search', '550');
Toolbar::spacer();
Toolbar::help('search');

Html::behavior('framework');

$context = array();
if (array_key_exists('search-task', $_POST))
{
	foreach (Event::trigger('search.onSearchTask' . $_POST['search-task']) as $resp)
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

foreach (Event::trigger('search.onSearchAdministrate', array($context)) as $plugin)
{
	list($name, $html) = $plugin;
	//echo '<h3>' . $name . '</h3>';
	echo $html;
}
