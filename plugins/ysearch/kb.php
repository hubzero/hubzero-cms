<?php

class plgYSearchKB extends YSearchPlugin
{
	public static function getName() { return 'Knowledge Base'; }

	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(f.title, f.`fulltext`) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(f.title LIKE '%$mand%' OR f.`fulltext` LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(f.title NOT LIKE '%$forb%' AND f.`fulltext` NOT LIKE '%$forb%')";

		$results->add(new YSearchResultSQL(
			"SELECT 
				f.title,
				concat(coalesce(f.`fulltext`, '')) AS description,
				concat('/kb/', coalesce(concat(s.alias, '/'), ''), f.alias) AS link,
				$weight AS weight,
				created AS date,
				CASE 
					WHEN s.alias IS NULL THEN c.alias
					WHEN c.alias IS NULL THEN s.alias
					ELSE concat(s.alias, ', ', c.alias) 
				END AS section
			FROM jos_faq f
			LEFT JOIN jos_faq_categories s 
				ON s.id = f.section
			LEFT JOIN jos_faq_categories c
				ON c.id = f.category
			WHERE 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		));
	}
}
