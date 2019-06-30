<?php
/**
* @package    hubzero-cms
* @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
* @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers;

/**
 * Publication Recommended Tagging class
 */

class RecommendedTags extends \Hubzero\Base\Obj
{
  public $_db  = null;

  public $has_focus_area = false;

	private $tags = array();

  private $focus_areas_map = array();

  // Ordered list of raw tags (no label in db).
  //   Example: array ( 0 => 'genetics', 1 => 'ecology', )
  private $existing_tags = array();

  // Boolean array of tags (no label in db).
  //   Example: array ( 'genetics' => true, 'ecology' => true, )
  private $existing_map = array();

  // Ordered list of recommended tags (label in db).
  //   Example: array ( 0 => array ( 'id' => '9', 'raw_tag' => 'Gravy', 'label' => 'Thanksgiving', ), 1 => array ( 'id' => '11', 'raw_tag' => 'Turkey', 'label' => 'Thanksgiving', ), 2 => array ( 'id' => '14', 'raw_tag' => 'Thighs', 'label' => 'Thanksgiving', ), )
  private $focus_areas = array();

  // Boolean array of recommended tags (label in db)
  //   Example: array ( 'gravy' => true, 'turkey' => true, 'thighs' => true, )
  private $existing_fa_map = array();

  // Array of focus area $fa_properties
  //   Example:  array ( 'Thanksgiving' => array ( 'raw_tag' => 'Thanksgiving', 'id' => '6', 'tag_id' => '8', 'mandatory_depth' => NULL, 'multiple_depth' => '1', ), )
  private $fa_properties = array();

  private $master_type = null;

	const ENDORSED_TAG = 2;
	const REGULAR_TAG  = 1;

	public function __construct($pid, $vid, $existing, $db, $opts = array())
	{
		$opts = array_merge(array(
			'min_len' => 4,
			'count'   => 20
		), $opts);

		$this->_db = $db;

    // Need to make sure we actually need to worry about focus areas
    $this->_db->setQuery(
      'SELECT master_type
       FROM #__publications
       WHERE id = ' . $pid
    );
    $this->master_type = (int) $this->_db->loadResult();
    $this->has_focus_area = !empty($this->loadFocusAreas());

		$this->_db->setQuery(
			'SELECT t.raw_tag, t.tag, f.*
       FROM #__focus_area_publication_master_type_rel fp
       INNER JOIN #__focus_areas f ON f.id = fp.focus_area_id
       INNER JOIN #__tags t ON t.id = f.tag_id
       WHERE fp.master_type_id = ' . $this->_db->quote($this->master_type)
		);
		$this->fa_properties = $this->_db->loadAssocList('raw_tag');

    // Make sure recommended tags are updated in the tags object table with the proper label.
    // Note that this SHOULD be done globally after removing or adding a focus area in com_tags,
    // but I'm being lazy now.
    $this->_updateTags($vid);

		$this->_db->setQuery(
			'SELECT t.id, raw_tag, (label IS NOT NULL AND label != "") AS is_focus_area, label
			FROM #__tags_object to1
			INNER JOIN #__tags t ON t.id = to1.tagid
			WHERE to1.tbl = \'publications\' AND to1.objectid = '.$vid
		);
		if (!$existing)
		{
			foreach ($this->_db->loadAssocList() as $tag)
			{
				if ($this->fa_properties && $tag['is_focus_area'])
				{
					$this->focus_areas[] = array_intersect_key($tag, array_flip(array('id', 'raw_tag', 'label')));
					$this->existing_fa_map[strtolower($tag['raw_tag'])] = true;
				}
				else
				{
					$this->existing_tags[] = $tag['raw_tag'];
					$this->existing_map[strtolower($tag['raw_tag'])] = true;
				}
			}
		}
		else {
			foreach ($existing as $tag)
			{
				if (!is_null($tag[2]))
				{
					$this->existing_fa_map[strtolower($tag[0])] = true;
				}
				else
				{
					$this->existing_tags[] = $tag[0];
					$this->existing_map[strtolower($tag[0])] = true;
				}
			}
		}

		$this->_db->setQuery('SELECT lower(raw_tag) AS raw_tag, CASE WHEN to1.id IS NULL THEN 0 ELSE 1 END AS is_endorsed
			FROM #__tags t
			LEFT JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.objectid = t.id AND to1.label = \'label\' AND to1.tagid = (SELECT id FROM #__tags WHERE tag = \'endorsed\')');

		$tags = array();
		foreach ($this->_db->loadAssocList() as $row)
		{
			$tags[\Hubzero\Utility\Inflector::singularize($row['raw_tag'])] = $row['is_endorsed'] ? self::ENDORSED_TAG : self::REGULAR_TAG;
			$tags[\Hubzero\Utility\Inflector::pluralize($row['raw_tag'])] = $row['is_endorsed'] ? self::ENDORSED_TAG : self::REGULAR_TAG;
		}

		// $this->tags = array();
	}

	public function get_tags()
	{
		return $this->tags;
	}
	public function get_existing_tags()
	{
		return $this->existing_tags;
	}
	public function get_existing_tags_map()
	{
		return $this->existing_map;
	}
	public function get_existing_tags_value_list()
	{
		static $val_list = array();
		if (!$val_list)
		{
			foreach ($this->existing_tags as $tag)
			{
				$val_list[] = str_replace('"', '&quot;', str_replace(',', '&#44;', $tag));
			}
		}
		return implode(',', $val_list);
	}
	public function get_focus_areas()
	{
		return $this->focus_areas;
	}
	public function get_focus_areas_map()
	{
		return $this->focus_areas_map;
	}
	public function get_existing_focus_areas_map()
	{
		return $this->existing_fa_map;
	}
	public function get_focus_area_properties()
	{
		return $this->fa_properties;
	}

  public function fa_controls($idx, $fas, $fa_props, $existing, $parent = NULL, $depth = 1)
	{
		foreach ($fas as $fa)
		{
			$props = $fa_props[$fa['label']];
			$multiple = !is_null($props['multiple_depth']) && $props['multiple_depth'] <= $depth;
			echo '<div class="fa'.($depth === 1 ? ' top-level' : '').'">';
			echo '<input class="option" class="'.($multiple ? 'checkbox' : 'radio').'" type="'.($multiple ? 'checkbox' : 'radio').'" '.(isset($existing[strtolower($fa['raw_tag'])]) ? 'checked="checked" ' : '' ).'id="tagfa-'.$idx.'-'.$fa['tag'].'" name="tagfa-'.$idx.($parent ? '-'.$parent : '').'[]" value="' . $fa['tag'] . '"';
			echo ' /><label style="display: inline;" for="tagfa-'.$idx.'-'.$fa['tag'].'"' . ($fa['description'] ? ' title="' . htmlentities($fa['description']) . '" class="tooltips"' : '') . '>'.$fa['raw_tag'].'</label>';
			if ($fa['children'])
			{
				echo $this->fa_controls($idx, $fa['children'], $fa_props, $existing, $fa['tag'], $depth + 1);
			}
			echo '</div>';
		}
	}

  // Make sure recommended tags are updated in the tags object table with the proper label.
  // This is done to ensure they show up in the recommended tags tree, not in the input field.
  // Note that this SHOULD be done globally after removing or adding a focus area in com_tags,
  // but I'm being lazy now.
  //
  // REFACTOR:  Add in error handling for db query.
  private function _updateTags($vid)
  {
    // First, do a global remove of label on pub tags
    $query = 'UPDATE #__tags_object
              SET label = \'\'
              WHERE objectid = ' . $vid . '
              AND tbl = \'publications\'';
    $this->_db->setQuery($query);
    $this->_db->query();

    // Now, IF there is a focus area, assign label to all existing
    //   tags in that focus area.
    foreach ($this->fa_properties as $focus_area => $fa_properties) {
      $fatree = $this->loadFocusAreas('\'' . $fa_properties['tag'] . '\'');
      $rtags = $this->flatten($fatree, 'id');
      if (!empty($rtags))
        {
        $query = 'UPDATE #__tags_object
                  SET label = ' . $this->_db->quote($focus_area) . '
                  WHERE objectid = ' . $vid . '
                  AND tbl = \'publications\'
                  AND tagid IN (' . implode(',', $rtags) .')';

        $this->_db->setQuery($query);
        $this->_db->query();
      }
    }
  }

  /**
   * Recursive method for loading hierarchical focus areas (tags)
   *
   * @param   integer  $id            Publication master_type ID
   * @param   array    $labels        Tags
   * @param   integer  $parent_id     Tag ID
   * @param   string   $parent_label  Tag
   * @return  void
   */
  public function loadFocusAreas($labels = null, $parent_id = null, $parent_label = null)
  {
    if (is_null($labels))
    {
      $this->_db->setQuery(
        'SELECT DISTINCT tag
        FROM #__focus_area_publication_master_type_rel fp
        INNER JOIN #__focus_areas f ON f.id = fp.focus_area_id
        INNER JOIN #__tags t ON t.id = f.tag_id
        WHERE fp.master_type_id = ' . $this->master_type
      );

      if (!($labels = $this->_db->loadColumn()))
      {
        return array();
      }
      $labels = '\'' . implode('\', \'', array_map(array($this->_db, 'escape'), $labels)) . '\'';
    }

    $this->_db->setQuery(
      $parent_id
        // get tags labeled focus area and parented by the tag identified by $parent_id
        ? 'SELECT DISTINCT t.raw_tag AS label, to2.ordering, t2.id, t2.tag, t2.raw_tag, t2.description
          FROM #__tags t
          INNER JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.tagid = t.id AND to1.label = \'label\'
          INNER JOIN #__tags_object to2 ON to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = to1.objectid
            AND to2.tagid = ' . $parent_id . '
          INNER JOIN #__tags t2 ON t2.id = to1.objectid
          WHERE t.raw_tag = ' . $this->_db->quote($parent_label) . '
          ORDER BY to2.ordering'
        // get tags that are labeled focus areas that are not also a parent of another tag labeled as a focus area
        : 'SELECT DISTINCT t.raw_tag AS label, to1.ordering, t2.id, t2.tag, t2.raw_tag, t2.description
          FROM #__tags t
          LEFT JOIN #__tags_object to1 ON to1.tagid = t.id AND to1.label = \'parent\' AND to1.tbl = \'tags\'
          INNER JOIN #__tags t2 ON t2.id = to1.objectid
          WHERE t.tag IN (' . $labels . ') AND (
            SELECT COUNT(*)
            FROM #__tags_object to2
            INNER JOIN #__tags_object to3 ON to3.tbl = \'tags\' AND to3.label = \'label\' AND to3.objectid = to2.tagid
            INNER JOIN #__tags t3 ON t3.id = to3.tagid AND t3.tag IN (' . $labels . ')
            WHERE to2.tbl = \'tags\' AND to2.label = \'parent\' AND to2.objectid = t2.id
            LIMIT 1
          ) = 0
          ORDER BY t.tag, CASE WHEN t2.raw_tag LIKE \'other%\' THEN 1 ELSE 0 END, t2.raw_tag'
    );
    $fas = $this->_db->loadAssocList('raw_tag');
    foreach ($fas as &$fa)
    {
      $fa['children'] = $this->loadFocusAreas($labels, $fa['id'], $fa['label']);
    }
    return $fas;
  }

  public function flatten($array, $filter = 'tag')
  {
    $flattened = array();
    array_walk_recursive($array, function($v, $k) use (&$flattened, $filter) {
      if ($k === $filter)
      {
        $flattened[] = $v;
      }
    });

    return $flattened;
  }

  public function checkStatus()
  {
    if ($this->has_focus_area) {
      $map = $this->get_existing_focus_areas_map();
      $fas = $this->get_focus_area_properties();
      array_walk($fas, function(&$v, $k) {
        $v['actual_depth'] = 0;
      });
      $rtags = $this->get_focus_areas();

      if ($fas) {
        // Calculate depth
        foreach ($rtags as $idx => $rtag)
        {
          $this->_db->setQuery(
            'SELECT lower(t.raw_tag) as raw_tag, t.id
            FROM `#__tags_object` to1
            INNER JOIN `#__tags` t ON t.id = to1.tagid
            INNER JOIN `#__tags_object` to2 ON to2.tagid = ' . $fas[$rtag['label']]['tag_id'] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
            WHERE to1.objectid = (SELECT id FROM jos_tags WHERE raw_tag = ' . $this->_db->quote($rtag['raw_tag']) . ') AND to1.tbl = \'tags\' AND to1.label = \'parent\''
          );
          $any_match = false;
          $parent = array();
          $possible_parents = $this->_db->loadAssocList();
          foreach ($possible_parents as $par)
          {
            if (isset($map[$par['raw_tag']]))
            {
              $parent[] = $par;
              $any_match = true;
            }
          }
          if (!$possible_parents || $any_match)
          {
            $filtered[] = $rtag;
            $parent_id = array();
            foreach ($parent as $par)
            {
              $parent_id[] = $par['id'];
            }
            if (isset($fas[$rtag['label']]) && $fas[$rtag['label']]['actual_depth'] < $fas[$rtag['label']]['mandatory_depth'])
            {
              // count depth if necessary to determine whether focus area constraints are satisified
              for ($depth = $parent ? 2 : 1; $parent_id && $fas[$rtag['label']]['actual_depth'] < $fas[$rtag['label']]['mandatory_depth'] && $depth < $fas[$rtag['label']]['mandatory_depth']; ++$depth)
              {
                $this->_db->setQuery(
                  'SELECT t.id
                  FROM `#__tags_object` to1
                  INNER JOIN `#__tags` t ON t.id = to1.tagid
                  INNER JOIN `#__tags_object` to2 ON to2.tagid = ' . $fas[$rtag['label']]['tag_id'] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
                  WHERE to1.objectid IN (' . implode(',', $parent_id) . ') AND to1.tbl = \'tags\' AND to1.label = \'parent\''
                );
                $parent_id = $this->_db->loadColumn();
              }
              $fas[$rtag['label']]['actual_depth'] = max($depth, $fas[$rtag['label']]['actual_depth']);
            }
          }
        }

        // Check depth
        foreach ($fas as $lbl => $fa)
    		{
    			if ($fa['actual_depth'] < $fa['mandatory_depth'])
    			{
            return 0;
    			}
    		}
      }
    }

    return 1;
  }

  /**
   * [Controller] Get the tags from the browser
   *
   * See: com_resources/controllers/create.php::step_tags_process()
   */
  public function processTags($pid, $vid)
  {
		$tags = preg_split('/,\s*/', $_POST['tags']);
		$push = array();
		$map  = array();

    $this->_db->setQuery(
      'SELECT fa.tag_id, t.raw_tag, fa.mandatory_depth AS minimum_depth, 0 AS actual_depth
      FROM `#__focus_areas` fa
      INNER JOIN `#__tags` t ON t.id = fa.tag_id
      INNER JOIN `#__focus_area_publication_master_type_rel` pmtr ON pmtr.focus_area_id = fa.id
      INNER JOIN `#__publication_master_types` pmt ON pmt.id = pmtr.master_type_id
      INNER JOIN `#__publications` p ON p.master_type = pmt.id AND p.id = ' . $pid . '
      WHERE fa.mandatory_depth IS NOT NULL AND fa.mandatory_depth > 0'
    );
    $fas = $this->_db->loadAssocList('raw_tag');

		foreach ($_POST as $k => $vs)
		{
			if (!preg_match('/^tagfa/', $k))
			{
				continue;
			}
			if (!is_array($vs))
			{
				$vs = array($vs);
			}
			foreach ($vs as $v)
			{
				$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($v));
				if (isset($map[$norm_tag]))
				{
					continue;
				}
				$this->_db->setQuery(
					'SELECT t2.raw_tag AS fa, t2.id AS label_id, t.id
					FROM `#__tags` t
					INNER JOIN `#__tags_object` to1 ON to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.objectid = t.id
					INNER JOIN `#__tags` t2 ON t2.id = to1.tagid
					INNER JOIN `#__focus_areas` fa ON fa.tag_id = to1.tagid
					WHERE t.tag = ' . $this->_db->quote($norm_tag)
				);
				if (($row = $this->_db->loadAssoc()))
				{
					$push[] = array($v, $norm_tag, $row['fa'], $row['id'], $row['label_id']);
					$map[$norm_tag] = true;
				}
			}
		}

		$filtered = array();
		// only accept focus areas with parents if their parent is also checked
		foreach ($push as $idx => $tag)
		{
			$this->_db->setQuery(
				'SELECT t.tag, t.id
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.tagid
				INNER JOIN `#__tags_object` to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
				WHERE to1.objectid = ' . $tag[3] . ' AND to1.tbl = \'tags\' AND to1.label = \'parent\''
			);
			$any_match = false;
			$parent = array();
			$possible_parents = $this->_db->loadAssocList();
			foreach ($possible_parents as $par)
			{
				if (isset($map[$par['tag']]))
				{
					$parent[] = $par;
					$any_match = true;
				}
			}
			if (!$possible_parents || $any_match)
			{
				$filtered[] = $tag;
				$parent_id = array();
				foreach ($parent as $par)
				{
					$parent_id[] = $par['id'];
				}
				if (isset($fas[$tag[2]]) && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth'])
				{
					// count depth if necessary to determine whether focus area constraints are satisified
					for ($depth = $parent ? 2 : 1; $parent_id && $fas[$tag[2]]['actual_depth'] < $fas[$tag[2]]['minimum_depth'] && $depth < $fas[$tag[2]]['minimum_depth']; ++$depth)
					{
						$this->_db->setQuery(
							'SELECT t.id
							FROM `#__tags_object` to1
							INNER JOIN `#__tags` t ON t.id = to1.tagid
							INNER JOIN `#__tags_object` to2 ON to2.tagid = ' . $tag[4] . ' AND to2.tbl = \'tags\' AND to2.objectid = to1.tagid
							WHERE to1.objectid IN (' . implode(',', $parent_id) . ') AND to1.tbl = \'tags\' AND to1.label = \'parent\''
						);
						$parent_id = $this->_db->loadColumn();
					}
					$fas[$tag[2]]['actual_depth'] = max($depth, $fas[$tag[2]]['actual_depth']);
				}
			}
			else
			{
				unset($map[$tag[1]]);
			}
		}
		$push = $filtered;

		foreach ($tags as $tag)
		{
			$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($tag));

			if (!$norm_tag || isset($map[$norm_tag]))
			{
				continue;
			}
			$push[] = array($tag, $norm_tag, '');
			$map[$norm_tag] = true;
		}
		foreach ($push as $idx => $tag)
		{
			$this->_db->setQuery("SELECT raw_tag FROM `#__tags` WHERE tag = " . $this->_db->quote($tag[1]));
			if (($raw_tag = $this->_db->loadResult()))
			{
				$push[$idx][0] = $raw_tag;
			}
		}

    // Going to manually do this like in Resources by deleting and then re-adding.
    // NOTE: This changes the time stamp!  Refactor:  Modify com_tags/models/cloud::setTags
    $rt = new \Components\Tags\Models\Cloud($vid, 'publications');
    $this->_db->setQuery('DELETE FROM `#__tags_object` WHERE tbl = \'publications\' AND objectid = ' . $vid);
    $this->_db->execute();
    foreach ($push as $tag)
		{
		  $rt->add($tag[0], User::get('id'), 0, 1, ($tag[2] ? $tag[2] : ''));
		}
  }
}
