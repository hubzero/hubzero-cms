<?php

class plgYSearchTags extends YSearchPlugin
{
	public static function onYSearchWidget($terms, &$widgets)
	{
		$weight = 'match(t.raw_tag, t.alias, t.description) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(t.raw_tag LIKE '%$mand%' OR t.alias LIKE '%$mand%' OR t.description LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(t.raw_tag NOT LIKE '%$forb%' AND t.alias NOT LIKE '%$forb%' AND t.description NOT LIKE '%$forb%')";

		$tags = new YSearchResultSQL(
			"SELECT 
				t.raw_tag AS title,
				description,
				concat('/tags/', t.tag) AS link,
				$weight AS weight,
				NULL AS date,
				'Tags' AS section
			FROM jos_tags t
			WHERE 
				NOT t.admin
				AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);

		$tag_html = array();
		foreach ($tags->to_associative() as $tag)
			$tag_html[] = '<li><a href="' . $tag->get_link() . '">' . $tag->get_title() . '</a></li>';
		$widgets->add_html('<ol class="tags">' . join('', $tag_html) . '</ol>');
	}
}
