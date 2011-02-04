<?php

function WikiBuildRoute(&$query)
{
	$segments = array();

	if (!empty($query['scope'])) {
		$segments[] = $query['scope'];
	}
	unset($query['scope']);
	if (!empty($query['pagename'])) {
		$segments[] = $query['pagename'];
	}
	unset($query['pagename']);

	return $segments;
}

function WikiParseRoute($segments)
{
	$vars = array();

	if (empty($segments))
		return $vars;

	//$vars['task'] = 'view';
	$e = array_pop($segments);
	$s = implode(DS,$segments);
	if ($s) {
		$vars['scope'] = $s;
	}
	$vars['pagename'] = $e;

	return $vars;
}

?>