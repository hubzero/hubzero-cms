<?php

class plgYSearchContent extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = 'match(c.title, c.introtext, c.`fulltext`) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(c.title LIKE '%$mand%' OR c.introtext LIKE '%$mand%' OR c.`fulltext` LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(c.title NOT LIKE '%$forb%' AND c.introtext NOT LIKE '%$forb%' AND c.`fulltext` NOT LIKE '%$forb%')";

		$sql = new YSearchResultSQL(
			"SELECT 
				c.title,
				concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, '')) AS description,
				CASE
					WHEN s.name OR ca.name OR c.alias THEN 
						concat(
							CASE WHEN s.name THEN concat('/', s.name) ELSE '' END, 
							CASE WHEN ca.name AND ca.name != s.name THEN concat('/', ca.name) ELSE '' END, 
							CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
						)
					ELSE concat('/content/article/', c.id) 
				END AS link,
				$weight AS weight,
				publish_up AS date,
				s.name AS section,
				(SELECT group_concat(u1.name separator '\\n') FROM jos_author_assoc anames INNER JOIN jos_users u1 ON u1.id = anames.authorid WHERE subtable = 'content' AND subid = c.id) AS contributors,
				(SELECT group_concat(ids.authorid separator '\\n') FROM jos_author_assoc ids WHERE subtable = 'content' AND subid = c.id) AS contributor_ids
			FROM jos_content c 
			LEFT JOIN jos_sections s 
				ON s.id = c.sectionid
			LEFT JOIN jos_categories ca
				ON ca.id = c.catid
			WHERE 
				(publish_up AND NOW() > publish_up) AND (NOT publish_down OR NOW() < publish_down) 
				AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		$results->add($sql);
	}
}
