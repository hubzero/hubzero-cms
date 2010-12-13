<?php

error_reporting(E_ALL);

class plgYSearchBlogs extends YSearchPlugin
{
	const FIRST_CLASS_CHILDREN = false;

	public static function onYSearch($request, &$results, $authz)
	{
		if ($authz->is_guest())
			$authorization = 'state = 1';
		else if ($authz->is_super_admin())
			$authorization = '1 = 1';
		else
			$authorization = 'state = 1 || state = 2';

		$terms = $request->get_term_ar();
		$weight = '(match(be.title, be.content) against (\''.join(' ', $terms['stemmed']).'\'))';
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(be.title LIKE '%$mand%' OR be.content LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(be.title NOT LIKE '%$forb%' AND be.content NOT LIKE '%$forb%')";

		$rows = new YSearchResultSQL(
			"SELECT 
				be.id,
				be.title,
				be.content AS description,
				concat('/members/', created_by, '/blog/', extract(year from be.created), '/', extract(month from be.created), '/', be.alias) AS link,
				$weight AS weight,
				'Blog Entry' AS section,
				be.created AS date,
				u.name AS contributors,
				created_by AS contributor_ids
			FROM #__blog_entries be
			INNER JOIN #__users u ON u.id = be.created_by
			WHERE
				$authorization AND
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);
		$rows = $rows->to_associative();
		$id_map = array();
		foreach ($rows as $idx=>$row)
			$id_map[$row->get('id')] = $idx;

		$comments = new YSearchResultSQL(
			"SELECT
		 	CASE WHEN bc.anonymous THEN 'Anonymous Comment' ELSE concat('Comment by ', u.name) END AS title,
			bc.content AS description,
			concat('/members/', be.created_by, '/blog/', extract(year from be.created), '/', extract(month from be.created), '/', be.alias) AS link,
			bc.created AS date,
			'Comments' AS section,
			bc.entry_id
			FROM #__blog_comments bc
			INNER JOIN #__blog_entries be
				ON be.id = bc.entry_id
			INNER JOIN #__users u
				ON u.id = bc.created_by
			WHERE bc.entry_id IN (".implode(',', array_keys($id_map)).")
			ORDER BY bc.created"
		);
		foreach ($comments->to_associative() as $comment)
			$rows->at($id_map[$comment->get('entry_id')])->add_child($comment);

		$results->add($rows);
	}
}
