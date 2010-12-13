<?php

class plgYSearchWishlists extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(wli.subject, wli.about) against(\''.join(' ', $terms['stemmed']).'\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(wli.subject LIKE '%$mand%' OR wli.about LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(wli.subject NOT LIKE '%$forb%' AND wli.about NOT LIKE '%$forb%')";

		$rows = new YSearchResultSQL(
			"SELECT 
				wli.subject AS title,
				wli.about AS description,
				concat('/wishlist/', wl.category, '/', wl.referenceid, '/wish/', wli.id) AS link,
				match(wli.subject, wli.about) against('collaboration') AS weight,
				wli.proposed AS date,
				concat(wl.title) AS section,
				CASE 
				WHEN wli.anonymous THEN NULL 
				ELSE (SELECT name FROM jos_users ju WHERE ju.id = wli.proposed_by) 
				END AS contributors,
				CASE 
				WHEN wli.anonymous THEN NULL 
				ELSE wli.proposed_by 
				END AS contributor_ids
			FROM jos_wishlist_item wli
			INNER JOIN jos_wishlist wl
				ON wl.id = wli.wishlist AND wl.public = 1
			WHERE 
				NOT wli.private AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) continue;
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}
}
