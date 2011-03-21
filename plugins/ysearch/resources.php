<?php

class ResourceChildSorter
{
	private $order;

	public function __construct($order)
	{
		$this->order = $order;
	}

	public function sort($a, $b)
	{
		$a_id = $a->get('id');
		$b_id = $b->get('id');
		$sec_diff = strcmp($a->get_section(), $b->get_section());
		if ($sec_diff < 0)
			return -1;
		if ($sec_diff > 0)
			return 1;
		$a_ord = $this->order[$a_id];
		$b_ord = $this->order[$b_id];
		return $a_ord == $b_ord ? 0 : $a_ord < $b_ord ? -1 : 1;
	}
}

class plgYSearchResources extends YSearchPlugin
{
	public static function onYSearch($request, &$results, $authz)
	{
		$dbg = isset($_GET['dbg']);
	
		if ($authz->is_guest())
			$access = 'access = 0';
		else if ($authz->is_super_admin())
			$access = '1';
		else
		{
			$groups = array_map('mysql_real_escape_string', $authz->get_group_names());
			if ($groups)
			{
				$group_list = '(\''.join('\', \'', $groups).'\')';
				$access = '(access = 0 OR access = 1 OR ((access = 3 OR access = 4) AND r.group_owner IN '.$group_list.'))';		
			}
			else 
				$access = '(access = 0 OR access = 1)';
		}

		$terms = $request->get_term_ar();
		$tag_map = array();
		foreach ($request->get_tagged_ids('resources') as $id)
			if (array_key_exists($id, $tag_map))
				++$tag_map[$id];
			else
				$tag_map[$id] = 1;

		$weight = 'match(r.title, r.introtext, r.`fulltext`) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(r.title LIKE '%$mand%' OR r.introtext LIKE '%$mand%' OR r.`fulltext` LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(r.title NOT LIKE '%$forb%' AND r.introtext NOT LIKE '%$forb%' AND r.`fulltext` NOT LIKE '%$forb%')";
		
		$sql = new YSearchResultSQL(
			"SELECT
				r.id,
				r.title,
				coalesce(r.`fulltext`, r.introtext, '') AS description,
				concat('/resources/', coalesce(case when r.alias = '' then null else r.alias end, r.id)) AS link,
				$weight AS weight,
				r.publish_up AS date,
				rt.type AS section,
				(SELECT group_concat(u1.name order by anames.ordering separator '\\n') FROM jos_author_assoc anames LEFT JOIN jos_xprofiles u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'resources' AND subid = r.id) 
				AS contributors,
				(SELECT group_concat(anames.authorid order by anames.ordering separator '\\n') FROM jos_author_assoc anames WHERE subtable = 'resources' AND subid = r.id) 
				AS contributor_ids,
				(select group_concat(concat(parent_id, '|', ordering)) 
					from jos_resource_assoc ra2 
					left join jos_resources re3 on re3.id = ra2.parent_id and re3.standalone 
					where ra2.child_id = r.id) AS parents
			FROM jos_resources r
			LEFT JOIN jos_resource_types rt 
				ON rt.id = r.type
			WHERE 
				r.published = 1 AND $access AND (r.publish_up AND NOW() > r.publish_up) AND (NOT r.publish_down OR NOW() < r.publish_down) 
				AND ($weight > 0)".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
		);
		$assoc = $sql->to_associative();

		$id_assoc = array();
		foreach ($assoc as $row)
			$id_assoc[$row->get('id')] = $row;
		
		$placed = array();
		// Find ids of tagged resources that did not match regular fulltext searching
		foreach ($assoc as $row)
		{
			$id = (int)$row->get('id');
			if (array_key_exists($id, $tag_map))
			{
				$row->adjust_weight((1 + $tag_map[$id])/4, 'tag bonus from resources plugin');
				unset($tag_map[$id]);
			}
		}
		// Fill in tagged resources that did not match on fulltext
		if ($tag_map)
		{
			$sql = new YSearchResultSQL(
	                        "SELECT
        	                        r.id,
                	                r.title,
					coalesce(r.`fulltext`, r.introtext, '') AS description,
	                                concat('/resources/', coalesce(case when r.alias = '' then null else r.alias end, r.id)) AS link,
                                	r.publish_up AS date,
					0.5 as weight,
	                                rt.type AS section,
					(SELECT group_concat(u1.name order by anames.ordering separator '\\n') FROM jos_author_assoc anames LEFT JOIN jos_xprofiles u1 ON u1.uidNumber = anames.authorid WHERE subtable = 'resources' AND subid = r.id) 
					AS contributors,
					(SELECT group_concat(anames.authorid order by anames.ordering separator '\\n') FROM jos_author_assoc anames WHERE subtable = 'resources' AND subid = r.id) 
					AS contributor_ids,
        	                        (select group_concat(concat(parent_id, '|', ordering))
                	                        from jos_resource_assoc ra2
                        	                left join jos_resources re3 on re3.id = ra2.parent_id and re3.standalone
                                	        where ra2.child_id = r.id) AS parents
		                        FROM jos_resources r
		                        LEFT JOIN jos_resource_types rt
                		                ON rt.id = r.type
		                        WHERE
                		                r.published = 1 AND $access AND (r.publish_up AND NOW() > r.publish_up) AND (NOT r.publish_down OR NOW() < r.publish_down)
					AND r.id in (".implode(',', array_keys($tag_map)).")".($addtl_where ? ' AND ' . implode(' AND ', $addtl_where) : '')
			);
			foreach ($sql->to_associative() as $row)
			{
				if ($tag_map[$row->get('id')] > 1)
					$row->adjust_weight($tag_map[$row->get('id')]/8, 'tag bonus for non-matching but tagged resources');
				$id_assoc[$row->get('id')] = $row;
			}
		}

		// Nest child resources
		$section = $request->get_terms()->get_section();
		foreach ($id_assoc as $id=>$row)
		{
			$parents = $row->get('parents');
			if ($parents)
				foreach (split(',', $parents) as $parent)
				{
					list($parent_id, $ordering) = split('\|', $parent);
					if (array_key_exists((int)$parent_id, $id_assoc) && $id_assoc[(int)$parent_id]->is_in_section($section, 'resources'))
					{
						$placed[(int)$id] = $ordering;
						$id_assoc[(int)$parent_id]->add_child($row);
						$id_assoc[(int)$parent_id]->add_weight($row->get_weight()/15, 'propagating child weight');
					}
				}
		}
	
		$sorter = new ResourceChildSorter($placed);
		$rows = array();
		foreach ($id_assoc as $id=>$row)
			if (!array_key_exists((int)$id, $placed))
			{
				$row->sort_children(array($sorter, 'sort'));
				$rows[] = $row;
			}

		usort($rows, create_function('$a, $b', 'return (($res = $a->get_weight() - $b->get_weight()) == 0 ? 0 : $res > 0 ? -1 : 1);'));
		foreach ($rows as $row)
			$results->add($row);
	}
}
