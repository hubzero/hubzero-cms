<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//var to hold content
$content = '';

//loop through each component and pages group passed in
foreach ($this->pages as $component)
{
	//build content to return
	$content .= '<h2>' . Lang::txt('COM_HELP_COMPONENT_HELP', $component['name']) . '</h2>';

	//make sure we have pages
	if (count($component['pages']) > 0)
	{
		$content .= '<p>' . Lang::txt('COM_HELP_PAGE_INDEX_EXPLANATION', $component['name']) . '</p>';
		$content .= '<ul>';
		foreach ($component['pages'] as $page)
		{
			$name = str_replace('.' . $this->layoutExt, '', $page);

			$content .= '<li><a href="' . Route::url('index.php?option=com_help&component=' . str_replace('com_', '', $component['option']) . '&page=' . $name) . '">' . ucwords(str_replace('_', ' ', $name)) .'</a></li>';
		}
		$content .= '</ul>';
	}
	else
	{
		$content .= '<p>' . Lang::txt('COM_HELP_NO_PAGES_FOUND') . '</p>';
	}
}

echo $content;
