<?php
jimport('joomla.application.component.model');

class YSearchModelRequest
{
	private $terms, $term_ar, $tags, $object_tags = array();

	public function get_terms() { return $this->terms; }
	public function get_term_ar() { return $this->term_ar; }
	public function get_tags() { return $this->tags; }
	public function get_tagged_ids($tbl)
	{
		return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : array();
	}
	public function get_tag_count_sql($tbl, $alias = NULL)
	{
		if (!array_key_exists($tbl, $this->object_tags) || !$this->object_tags[$tbl])
			return '(0)';

		if (is_null($alias))
			$alias = $tbl;

		return '(count_in_list('.$alias.'.id, \''.join(',', $this->object_tags[$tbl]).'\'))';
		
		$tag_sql = array();
		foreach ($this->object_tags[$tbl] as $id)
			$tag_sql[] = "CASE WHEN {$alias}.id = {$id} THEN 1 ELSE 0 END";
		return '('.join(' + ', $tag_sql).')';
	}
	public function get_tag_ids_by_table($tbl)
	{
		return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : array();
	}

	public function __construct($terms)
	{
		$this->terms = $terms;
		$this->term_ar = array(
			'mandatory' => $this->terms->get_mandatory_chunks(),
			'optional' => $this->terms->get_optional_chunks(),
			'forbidden' => $this->terms->get_forbidden_chunks(),
			'stemmed' => $this->terms->get_stemmed_chunks()
		);
		$this->load_tags();
	}

	private function load_tags()
	{
		$weight = 'match(t.raw_tag, t.alias, t.description) against (\''.join(' ', $this->term_ar['stemmed']).'\')';
			
		$addtl_where = array();
		foreach ($this->term_ar['mandatory'] as $mand)
			$addtl_where[] = "(t.raw_tag LIKE '%$mand%' OR t.alias LIKE '%$mand%' OR t.description LIKE '%$mand%')";
		foreach ($this->term_ar['forbidden'] as $forb)
			$addtl_where[] = "(t.raw_tag NOT LIKE '%$forb%' AND t.alias NOT LIKE '%$forb%' AND t.description NOT LIKE '%$forb%')";

		$tags = new YSearchResultSQL(
			"SELECT 
				id,
				t.raw_tag AS title,
				description,
				concat('/tags/', t.tag) AS link,
				$weight AS weight,
				NULL AS date,
				'Tags' AS section
			FROM jos_tags t
			WHERE 
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		$this->tags = $tags->to_associative();

		$tag_ids = array();
		foreach ($this->tags as $tag)
			$tag_ids[] = $tag->get('id');

		if ($tag_ids)
		{
			$dbh =& JFactory::getDBO();
			$dbh->setQuery(
				'SELECT objectid, tbl FROM jos_tags_object WHERE tagid IN ('.join(',', $tag_ids).')'
			);
			foreach ($dbh->loadAssocList() as $row)
			{
				if (!array_key_exists($row['tbl'], $this->object_tags))
					$this->object_tags[$row['tbl']] = array();
				$this->object_tags[$row['tbl']][] = $row['objectid'];
			}
		}
	}
}
