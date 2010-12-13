<?php

class plgYSearchDocuments extends YSearchPlugin
{
	public static function onYSearch($request, &$results) 
	{
		$terms = $request->get_term_ar();
		$weight = '(match(d.text_content) AGAINST (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(d.text_content LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(d.text_content NOT LIKE '%$forb%')";

		$results->add(new YSearchResultSQL(
			"SELECT
				sd.id,
				concat('Unknown: ', d.hash) AS title,
				text_content AS description,
				concat('/documents/', id) AS link,
				$weight AS weight,
				sd.created AS date,
				'Documents' AS section
			FROM jos_document_text_content d
			INNER JOIN jos_searchable_documents sd ON sd.hash = d.hash
			WHERE $weight > 0
			ORDER BY $weight DESC"
		));
	}
}
