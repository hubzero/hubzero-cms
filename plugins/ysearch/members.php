<?php

class ContributionSorter 
{
	public static function sort($a, $b)
	{
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
			return -1;
		if ($sec_diff > 0)
			return 1;
		$a_ord = $a->get('ordering');
		$b_ord = $b->get('ordering');
		return $a_ord == $b_ord ? 0 : $a_ord < $b_ord ? -1 : 1;
	}

	public static function sort_weight($a, $b)
	{
		$aw = $a->get_weight();
		$bw = $b->get_weight();
		if ($aw == $bw) return 0;
		return $aw > $bw ? -1 : 1;
	}
}

class plgYSearchMembers extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$weight = '(match(p.name) against (\''.join(' ', $terms['stemmed']).'\') + match(b.bio) against(\''.join(' ', $terms['stemmed']) .'\'))';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(p.name LIKE '%$mand%' OR b.bio LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(p.name NOT LIKE '%$forb%' AND b.bio NOT LIKE '%$forb%')";

		$results->add(new YSearchResultSQL(
			"SELECT 
				p.uidNumber AS id,
				p.name AS title,
				coalesce(b.bio, '') AS description,
				concat('/members/', CASE WHEN p.uidNumber > 0 THEN p.uidNumber ELSE concat('n', abs(p.uidNumber)) END) AS link,
				$weight AS weight,
				NULL AS date,
				'Members' AS section,
        CASE WHEN p.picture IS NOT NULL THEN concat('/site/members/', lpad(p.uidNumber, 5, '0'), '/', p.picture) ELSE NULL END AS img_href
			FROM jos_xprofiles p
			INNER JOIN jos_users u
				ON u.id = p.uidNumber AND u.block = 0
			LEFT JOIN jos_xprofiles_bio b 
				ON b.uidNumber = p.uidNumber
			WHERE 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		));
	}

	public static function onYSearchCustom($request, &$results)
	{
		if (($section = $request->get_terms()->get_section()) && $section[0] != 'members')
			return;
 
		$terms = $request->get_term_ar();
		$addtl_where = array();
		foreach (array($terms['mandatory'], $terms['optional']) as $pos)
			foreach ($pos as $term)
				$addtl_where[] = "(p.name LIKE '%$term%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(p.name NOT LIKE '%$forb%')";

		$sql = new YSearchResultSQL(
			"SELECT 
				p.uidNumber AS id,
				p.name AS title,
				coalesce(b.bio, '') AS description,
				concat('/members/', CASE WHEN p.uidNumber > 0 THEN p.uidNumber ELSE concat('n', abs(p.uidNumber)) END) AS link,
				NULL AS date,
				'Members' AS section,
        CASE WHEN p.picture IS NOT NULL THEN concat('/site/members/', lpad(p.uidNumber, 5, '0'), '/', p.picture) ELSE NULL END AS img_href
			FROM jos_xprofiles p
			INNER JOIN jos_users u
				ON u.id = p.uidNumber AND u.block = 0
			LEFT JOIN jos_xprofiles_bio b 
				ON b.uidNumber = p.uidNumber
			WHERE 
				" . join(' AND ', $addtl_where)
		);
		$assoc = $sql->to_associative();
		if (!count($assoc))
			return false;

		$resp = array();
		foreach ($assoc as $row)
		{
			$work = new YSearchResultSQL(
				"SELECT 
					r.title,
					CASE 
						WHEN aa.subtable = 'resources' THEN
							concat(coalesce(r.introtext, ''), coalesce(r.`fulltext`, '')) 
						ELSE
							concat(coalesce(c.introtext, ''), coalesce(c.`fulltext`, ''))
					END AS description,
					CASE
						WHEN aa.subtable = 'resources' THEN
							concat('/resources/', r.id)
						ELSE
							CASE
								WHEN s.name OR ca.name OR c.alias THEN 
									concat(
										CASE WHEN s.name THEN concat('/', s.name) ELSE '' END, 
										CASE WHEN ca.name AND ca.name != s.name THEN concat('/', ca.name) ELSE '' END, 
										CASE WHEN c.alias THEN concat('/', c.alias) ELSE '' END
									)
								ELSE concat('/content/article/', c.id) 
							END
					END AS link,
					1 AS weight,
					CASE 
						WHEN aa.subtable = 'resources' THEN
							rt.type
						ELSE
							s.name
					END AS section,
					CASE
						WHEN aa.subtable = 'resources' THEN
							ra.ordering
						ELSE
							-1
					END AS ordering
					FROM jos_author_assoc aa
					LEFT JOIN jos_resources r
						ON aa.subtable = 'resources' AND r.id = aa.subid AND r.published = 1
					LEFT JOIN jos_resource_assoc ra
						ON ra.child_id = r.id
					LEFT JOIN jos_resource_types rt 
						ON rt.id = r.type
					LEFT JOIN jos_content c
						ON aa.subtable = 'content' AND c.id = aa.subid
					LEFT JOIN jos_sections s 
						ON s.id = c.sectionid
					LEFT JOIN jos_categories ca
						ON ca.id = c.catid
					WHERE aa.authorid = ".$row->get('id')
			);
			$work_assoc = $work->to_associative();
			$added = array();
			foreach ($work_assoc as $wrow)
			{
				$link = $wrow->get_link();
				if (array_key_exists($link, $added))
					continue;

				$row->add_child($wrow);
				$row->add_weight(1);
				$added[$link] = 1;
			}
			$row->sort_children(array('ContributionSorter', 'sort'));
			$resp[] = $row;
		}
		usort($resp, array('ContributionSorter', 'sort_weight'));
		foreach ($resp as $row)
			$results->add($row);
		return false;
	}

  public static function onBeforeYSearchRenderMembers($res)
  {
    if (!($href = $res->get('img_href')) || !is_file(JPATH_ROOT.$href))
      $href = '/components/com_members/images/profile_thumb.gif';

    return '<img src="'.$href.'" alt="'.htmlentities($res->get_title()).'" title="'.htmlentities($res->get_title()).'" />';
  }
}
