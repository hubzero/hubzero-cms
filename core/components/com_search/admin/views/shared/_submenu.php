<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$option = $this->option;
$receivedQuery = Request::query();

if (!isset($receivedQuery['controller']))
{
	$receivedQuery['controller'] = null;
}

$entries = [
	[
		"text" => Lang::txt('COM_SEARCH_SUBMENU_OVERVIEW'),
		"queryParams" => [
			"option" => $option,
			"task" => "configure",
			"controller" => null
		]
	],
	[
		"text" => Lang::txt('COM_SEARCH_SUBMENU_COMPONENTS'),
		"queryParams" => [
			"option" => $option,
			"task" => "display",
			"controller" => "searchable"
		]
	],
	[
		"text" => Lang::txt('COM_SEARCH_SUBMENU_BLACKLIST'),
		"queryParams" => [
			"option" => $option,
			"task" => "manageBlacklist",
			"controller" => "solr"
		]
	],
	[
		"text" => Lang::txt('COM_SEARCH_SUBMENU_BOOSTS'),
		"queryParams" => [
			"option" => $option,
			"task" => "list",
			"controller" => "boosts"
		]
	]
];

foreach ($entries as $entry)
{
	$text = $entry['text'];
	$params = $entry['queryParams'];
	$queryString = http_build_query($params);
	$url = "index.php?$queryString";

	$active = $params['controller'] == $receivedQuery['controller'];

	Submenu::addEntry($text, $url, $active);
}
