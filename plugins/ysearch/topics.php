<?php

class plgYSearchTopics extends YSearchPlugin
{
	public static function onYSearch($request, &$results, $authz)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(wp.title) against (\''.join(' ', $terms['stemmed']).'\') + match(wv.pagetext) against (\''.join(' ', $terms['stemmed']).'\'))';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(wp.title LIKE '%$mand%' OR wv.pagetext LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(wp.title NOT LIKE '%$forb%' AND wv.pagetext NOT LIKE '%$forb%')";

		# TODO
		if ($authz->is_guest())
			$authorization = 'wp.access = 0';
		elseif ($authz->is_super_admin())
			$authorization = '1';
		elseif (($gids = $authz->get_group_ids()))
			$authorization = '(wp.access = 0 OR (wp.access = 1 AND xg.gidNumber IN ('.join(',', $gids).')))';
		else
			$authorization = 'wp.access = 0';

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
			LEFT JOIN jos_xgroups xg ON xg.cn = wp.group
			WHERE
				$authorization AND
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
