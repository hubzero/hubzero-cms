<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

use Components\Search\Models\Basic\Result\Sql;

/**
 * Search request model
 */
class Request
{
	/**
	 * Description for 'terms'
	 *
	 * @var object
	 */
	private $terms, $term_ar, $tags, $object_tags = array();

	/**
	 * Short description for 'get_terms'
	 *
	 * Long description (if any) ...
	 *
	 * @return     object Return description (if any) ...
	 */
	public function get_terms()
	{
		return $this->terms;
	}

	/**
	 * Short description for 'get_term_ar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_term_ar()
	{
		return $this->term_ar;
	}

	/**
	 * Short description for 'get_tags'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_tags()
	{
		return $this->tags;
	}

	/**
	 * Short description for 'get_tagged_ids'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get_tagged_ids($tbl)
	{
		return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : array();
	}

	/**
	 * Short description for 'get_tag_ids_by_table'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function get_tag_ids_by_table($tbl)
	{
		return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : array();
	}

	/**
	 * Constructor
	 *
	 * @param      object $terms Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($terms)
	{
		$this->terms = $terms;
		$this->term_ar = array(
			'mandatory' => $this->terms->get_mandatory_chunks(),
			'optional'  => $this->terms->get_optional_chunks(),
			'forbidden' => $this->terms->get_forbidden_chunks(),
			'stemmed'   => $this->terms->get_stemmed_chunks()
		);
		$this->load_tags();
	}

	/**
	 * Short description for 'load_tags'
	 *
	 * Long description (if any) ...
	 *
	 * @return     void
	 */
	private function load_tags()
	{
		$weight = 'match(t.raw_tag, t.description) against (\'' . join(' ', $this->term_ar['stemmed']) . '\')';

		$addtl_where = array();
		foreach ($this->term_ar['mandatory'] as $mand)
		{
			$addtl_where[] = "(t.raw_tag LIKE '%$mand%' OR t.tag LIKE '%$mand%' OR t.description LIKE '%$mand%')";
		}
		foreach ($this->term_ar['forbidden'] as $forb)
		{
			$addtl_where[] = "(t.raw_tag NOT LIKE '%$forb%' AND t.tag NOT LIKE '%$forb%' AND t.description NOT LIKE '%$forb%')";
		}

		$tags = new Sql(
			"SELECT
				id,
				t.raw_tag AS title,
				description,
				concat('index.php?option=com_tags&tag=', t.tag) AS link,
				$weight AS weight,
				NULL AS date,
				'Tags' AS section
			FROM `#__tags` t
			WHERE
				$weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '') .
			" ORDER BY $weight DESC"
		);
		$this->tags = $tags->to_associative();

		$tag_ids = array();
		foreach ($this->tags as $tag)
		{
			$tag_ids[] = $tag->get('id');
		}

		if ($tag_ids)
		{
			$dbh = \App::get('db');
			$dbh->setQuery(
				'SELECT objectid, tbl FROM `#__tags_object` WHERE tagid IN (' . join(',', $tag_ids) . ')'
			);
			foreach ($dbh->loadAssocList() as $row)
			{
				if (!array_key_exists($row['tbl'], $this->object_tags))
				{
					$this->object_tags[$row['tbl']] = array();
				}
				$this->object_tags[$row['tbl']][] = $row['objectid'];
			}
		}
	}
}
