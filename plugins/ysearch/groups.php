<?php

class plgYSearchGroups extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(g.cn, g.description, g.public_desc) AGAINST (\''.join(' ', $terms['stemmed']).'\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(g.cn LIKE '%$mand%' OR g.description LIKE '%$mand%' OR g.public_desc LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(g.cn NOT LIKE '%$forb%' AND g.description NOT LIKE '%$forb%' AND g.public_desc NOT LIKE '%$forb%')";

		$results->add(new YSearchResultSQL(
			"SELECT
				g.description AS title,
				coalesce(g.public_desc, '') AS description,
				concat('/groups/', g.cn) AS link,
				$weight AS weight,
				NULL AS date,
				'Groups' AS section
			FROM jos_xgroups g
			WHERE
				g.type = 1 AND g.privacy <= 1 AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		));
	}
}
