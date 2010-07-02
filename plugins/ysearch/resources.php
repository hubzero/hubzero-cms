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
	public static function onYSearch($request, &$results)
	{
		$terms = $request->get_term_ar();
		$tag_count = $request->get_tag_count_sql('resources', 'r');
		$weight = 'match(r.title, r.introtext, r.`fulltext`) against (\''.join(' ', $terms['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(r.title LIKE '%$mand%' OR r.introtext LIKE '%$mand%' OR r.`fulltext` LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(r.title NOT LIKE '%$forb%' AND r.introtext NOT LIKE '%$forb%' AND r.`fulltext` NOT LIKE '%$forb%')";
		
		# (select group_concat(child_id) from jos_resource_assoc ra inner join jos_resources re2 on re2.id = ra.child_id and re2.standalone where ra.parent_id = r.id) AS children,
		$sql = new YSearchResultSQL(
			"SELECT
				r.id,
				r.title,
				concat(coalesce(r.introtext, ''), coalesce(r.`fulltext`, '')) AS description,
				concat('/resources/', coalesce(case when r.alias = '' then null else r.alias end, r.id)) AS link,
				$weight AS weight,
				$tag_count AS tag_count,
				r.publish_up AS date,
				rt.type AS section,
				(SELECT group_concat(u1.name separator '\\n') FROM jos_author_assoc anames INNER JOIN jos_users u1 ON u1.id = anames.authorid WHERE subtable = 'resources' AND subid = r.id) AS contributors,
				(SELECT group_concat(ids.authorid separator '\\n') FROM jos_author_assoc ids WHERE subtable = 'resources' AND subid = r.id) AS contributor_ids,
				(select group_concat(concat(parent_id, '|', ordering)) 
					from jos_resource_assoc ra2 
					left join jos_resources re3 on re3.id = ra2.parent_id and re3.standalone 
					where ra2.child_id = r.id) AS parents
			FROM jos_resources r
			LEFT JOIN jos_resource_types rt 
				ON rt.id = r.type
			WHERE 
				r.published AND r.standalone AND NOT r.access AND (r.publish_up AND NOW() > r.publish_up) AND (NOT r.publish_down OR NOW() < r.publish_down) 
				AND ($weight > 0 OR $tag_count > 0)".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		$assoc = $sql->to_associative();

		$id_assoc = array();
		foreach ($assoc as $row)
			$id_assoc[$row->get('id')] = $row;
		
		$placed = array();
		foreach ($assoc as $row)
		{
			$parents = $row->get('parents');
			if ($parents)
				foreach (split(',', $parents) as $parent)
				{
					list($parent_id, $ordering) = split('\|', $parent);
					if (array_key_exists((int)$parent_id, $id_assoc))
					{
						$placed[(int)$row->get('id')] = $ordering;
						$id_assoc[(int)$parent_id]->add_child($row);
						$id_assoc[(int)$parent_id]->add_weight($row->get_weight()/10);
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
