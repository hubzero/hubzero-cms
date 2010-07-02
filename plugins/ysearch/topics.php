<?php

class plgYSearchTopics extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(wp.title) against (\''.join(' ', $terms['stemmed']).'\') + match(wv.pagetext) against (\''.join(' ', $terms['stemmed']).'\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(wp.title LIKE '%$mand%' OR wv.pagetext LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(wp.title NOT LIKE '%$forb%' AND wv.pagetext NOT LIKE '%$forb%')";

		$juser =& JFactory::getUser();
		$xuser =& XFactory::getUser();
		$app =& JFactory::getApplication();

		# TODO
		if ($juser->get('guest') || !is_object($xuser) || !in_array(strtolower($app->getCfg('sitename', $xuser->get('admin')))))
			$authorization = 'access = 0 AND';

		$rows = new YSearchResultSQL(
			"SELECT 
				wp.title,
				wv.pagetext AS description,
				CASE 
					WHEN wp.group THEN concat('index.php?option=com_groups&scope=', wp.scope, '&pagename=', wp.pagename)
					ELSE concat('index.php?option=com_topics&scope=', wp.scope, '&pagename=', wp.pagename)
				END AS link,
				$weight AS weight,
				wv.created AS date,
				'Topics' AS section
			FROM jos_wiki_version wv
			INNER JOIN jos_wiki_page wp 
				ON wp.id = wv.pageid
			WHERE
				$authorization
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" GROUP BY wv.pageid 
			ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) continue;
			# rough de-wikifying. probably a bit faster than rendering to html and then stripping the tags, but not perfect
			$row->set_link(JRoute::_($row->get_raw_link()));
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}

	}
}
