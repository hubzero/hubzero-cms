<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Tags controller class for managing raltionships between tags
 */
class TagsControllerRelationships extends \Hubzero\Component\AdminController
{
	private $preload;

	/**
	 * Show a form for looking up a tag's relationships
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->preload = 'nikki';
		$this->view->setLayout('display');

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		
		if (isset($_REQUEST['tag']) && (int)$_REQUEST['tag'] == $_REQUEST['tag']) {
			$this->database->setQuery('SELECT tag FROM #__tags WHERE id = '.$_REQUEST['tag']);
			$this->view->set('preload', $this->database->loadResult());
		}
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Show a form for managing focus areas
	 * 
	 * @return     void
	 */
	public function metaTask()
	{
		$this->view->setLayout('meta');

		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Implicit relationship lookup
	 * Generates data in JSON format
	 * 
	 * @return     void
	 */
	public function implicitTask()
	{
		$tag = isset($_GET['tag']) ? $_GET['tag'] : 0;

		$nodes = array();
		$links = array();
		$id = null;
		$descr = '';

		$rv = $tag = $this->get_tag($tag);
		$nodes[] = array(
			'id'      => $rv['id'], 
			'tag'     => $rv['tag'], 
			'raw_tag' => $rv['raw_tag']
		);
		if (!$rv['new'])
		{
			$this->database->setQuery(
				'SELECT t.id, t.tag, t.raw_tag, count(t.id) AS count FROM #__tags_object to1
				LEFT JOIN #__tags_object to2 ON to2.tbl = to1.tbl AND to2.objectid = to1.objectid
				INNER JOIN #__tags t ON t.id = to2.tagid
				WHERE to1.tagid = ' . $rv['id'] . ' AND t.id != ' . $rv['id'] . ' AND to1.label IS NULL   
				GROUP BY t.id, t.tag, t.raw_tag
				ORDER BY count DESC
				LIMIT 20'
			);
			$t_idx = 0;
			$idx_map = array($rv['id'] => $t_idx);
			$follow = array();
			$max_weight = null;
			foreach ($this->database->loadAssocList() as $idx => $row)
			{
				if (is_null($max_weight))
				{
					$max_weight = $row['count'];
				}

				if ($row['count'] == 1)
				{
					break;
				}
				$nodes[] = $row;
				$idx_map[$row['id']] = ++$t_idx;
				$links[] = array(
					'source' => $idx + 1, 
					'target' => 0, 
					'value'  => $row['count']
				);
				$follow[$idx + 1] = $row['id'];
			}
			foreach ($follow as $idx=>$tag_id)
			{
				$this->database->setQuery(
					'SELECT t.id, t.tag, t.raw_tag, count(t.id) AS count FROM #__tags_object to1
					LEFT JOIN #__tags_object to2 ON to2.tbl = to1.tbl AND to2.objectid = to1.objectid
					INNER JOIN #__tags t ON t.id = to2.tagid
					WHERE to1.tagid = ' . $tag_id . ' AND t.id != ' . $tag_id . ' AND to1.label IS NULL 
					GROUP BY t.id, t.tag, t.raw_tag
					ORDER BY count DESC
					LIMIT 10'
				);
				foreach ($this->database->loadAssocList() as $inner_idx => $row)
				{
					$max_weight = max($max_weight, $row['count']);
					if ($row['count'] == 1)
					{
						break;
					}
					if (isset($idx_map[$row['id']]))
					{
						$target_idx = $idx_map[$row['id']];
					}
					else
					{
						$nodes[] = $row;
						$target_idx = ++$t_idx;
						$idx_map[$row['id']] = $t_idx;
					}
					$links[] = array(
						'source' => $idx, 
						'target' => $target_idx, 
						'value'  => $row['count']
					);
				}
				foreach ($links as &$link)
					$link['value'] /= $max_weight;
			}
		}
		header('Content-type: application/octet-stream');
		$rv['nodes'] = $nodes;
		$rv['links'] = $links;
		echo json_encode($rv);
		exit();
	}

	/**
	 * Hierarchical relationship lookup
	 * Generates data in JSON format
	 * 
	 * @return     void
	 */
	public function hierarchyTask()
	{
		$tag = isset($_GET['tag']) ? $_GET['tag'] : 0;

		$nodes = array();
		$links = array();
		$id = null;
		$descr = '';

		$rv = $tag =  $this->get_tag($tag);
		$nodes[] = array(
			'id'      => $rv['id'], 
			'tag'     => $rv['tag'], 
			'raw_tag' => $rv['raw_tag']
		);
		if (!$rv['new'] && isset($tag['id']))
		{
			$t_idx = 0;
			$idx_map = array($tag['id'] => $t_idx);
			$this->database->setQuery(
				'SELECT DISTINCT t.id, t.tag, t.raw_tag, to1.label
				FROM #__tags_object to1
				INNER JOIN #__tags t ON t.id = to1.tagid
				WHERE to1.label IN (\'parent\', \'label\') AND to1.tbl = \'tags\' AND to1.objectid = ' . $rv['id'] . ' 
				UNION
				SELECT DISTINCT t.id, t.tag, t.raw_tag, to1.label
				FROM #__tags_object to1
				INNER JOIN #__tags t ON t.id = to1.objectid
				WHERE to1.label = \'label\' AND to1.tbl = \'tags\' AND to1.tagid = ' . $rv['id']
			);
			foreach ($this->database->loadAssocList() as $row)
			{
				$idx_map[$row['id']] = ++$t_idx;
				$row['type'] = $row['label'];
				$nodes[] = $row;
				$links[] = array('source' => $t_idx, 'target' => 0);
			}

			$this->database->setQuery(
				'SELECT DISTINCT t.id, t.tag, t.raw_tag
				FROM #__tags_object to1
				INNER JOIN #__tags t ON t.id = to1.objectid
				WHERE to1.label = \'parent\' AND to1.tbl = \'tags\' AND to1.tagid = ' . $rv['id']
			);
			foreach ($this->database->loadAssocList() as $row)
			{
				$idx_map[$row['id']] = ++$t_idx;
				$row['type'] = 'child';
				$nodes[] = $row;
				$links[] = array('source' => $t_idx, 'target' => 0);
				$this->database->setQuery(
					'SELECT DISTINCT t.id, t.tag, t.raw_tag
					FROM #__tags_object to1
					INNER JOIN #__tags t ON t.id = to1.objectid
					WHERE to1.label = \'parent\' AND to1.tbl = \'tags\' AND to1.tagid = ' . $row['id']
				);
				foreach ($this->database->loadAssocList() as $inner_row)
				{
					$idx_map[$inner_row['id']] = ++$t_idx;
					$inner_row['type'] = 'child';
					$nodes[] = $inner_row;
					$links[] = array('source' => $t_idx, 'target' => $idx_map[$row['id']]);
					$this->database->setQuery(
						'SELECT DISTINCT t.id, t.tag, t.raw_tag
						FROM #__tags_object to1
						INNER JOIN #__tags t ON t.id = to1.objectid
						WHERE to1.label = \'parent\' AND to1.tbl = \'tags\' AND to1.tagid = ' . $inner_row['id']
					);
					foreach ($this->database->loadAssocList() as $inner_inner_row)
					{
						$idx_map[$inner_inner_row['id']] = ++$t_idx;
						$inner_inner_row['type'] = 'child';
						$nodes[] = $inner_inner_row;
						$links[] = array('source' => $t_idx, 'target' => $idx_map[$inner_row['id']]);
					}
				}
			}
		}

		header('Content-type: application/octet-stream');
		$rv['nodes'] = $nodes;
		$rv['links'] = $links;
		echo json_encode($rv);
		exit();
	}

	/**
	 * Tag suggester for autocompletion
	 * Generates data in JSON format
	 * 
	 * @return     void
	 */
	public function suggestTask()
	{
		$suggestions = array();

		if (isset($_GET['term']))
		{
			$this->database->setQuery('SELECT raw_tag FROM #__tags WHERE MATCH(raw_tag) AGAINST (\'*' . $this->database->getEscaped($_GET['term']) . '*\' IN BOOLEAN MODE)');
			$later = array();
			foreach ($this->database->loadResultArray() as $tag)
			{
				$tag = stripslashes($tag);
				if (strpos($tag, $_GET['term']) === 0)
				{
					$suggestions[] = $tag;
				}
				else
				{
					$later[] = $tag;
				}
			}
			sort($suggestions);
			sort($later);
			$suggestions = array_merge($suggestions, $later);
			if (isset($_GET['limit']))
			{
				$suggestions = array_slice($suggestions, 0, (int)$_GET['limit']);
			}
		}
		header('Content-type: application/octet-stream'); 
		echo json_encode($suggestions);
		exit();
	}

	/**
	 * Update a tag's relationships
	 * 
	 * @return     void
	 */
	public function updateTask()
	{
		if (isset($_POST['tag']) && ($tid = (int)$_POST['tag']))
		{
			$this->database->setQuery('UPDATE #__tags SET description = ' . $this->database->quote($_POST['description']) . ' WHERE id = ' . $tid);
			$this->database->execute();
			$tag = $this->get_tag($tid);
			$preload = $tag['raw_tag'];
			$normalize = create_function('$a', 'return preg_replace(\'/[^a-zA-Z0-9]/\', \'\', strtolower($a));'); 
			// reconcile post data with what we already know about a tag's relationships
			foreach (array(
				'labels'   => array(
					'INSERT INTO #__tags_object(tbl, label, tagid, objectid) VALUES (\'tags\', \'label\', %d, %d)',
					'DELETE FROM #__tags_object WHERE tbl = \'tags\' AND label = \'label\' AND tagid = %d AND objectid = %d'
				),
				'labeled'  => array(
					'INSERT INTO #__tags_object(tbl, label, objectid, tagid) VALUES (\'tags\', \'label\', %d, %d)',
					'DELETE FROM #__tags_object WHERE tbl = \'tags\' AND label = \'label\' AND objectid = %d AND tagid = %d'
				),
				'parents'  => array(
					'INSERT INTO #__tags_object(tbl, label, objectid, tagid) VALUES (\'tags\', \'parent\', %d, %d)',
					'DELETE FROM #__tags_object WHERE tbl = \'tags\' AND label = \'parent\' AND objectid = %d AND tagid = %d'
				),
				'children' => array(
					'INSERT INTO #__tags_object(tbl, label, tagid, objectid) VALUES (\'tags\', \'parent\', %d, %d)',
					'DELETE FROM #__tags_object WHERE tbl = \'tags\' AND label = \'parent\' AND tagid = %d AND objectid = %d'
				)) as $type => $sql)
			{
				$ex = array_flip(array_map($normalize, $tag[$type]));
				if (isset($_POST[$type]))
				{
					foreach ($_POST[$type] as $n_tag)
					{
						$norm_n_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($n_tag));

						// co-occurring tags neither need to be added nor deleted, just remove them from the pool and carry on
						if (isset($ex[$norm_n_tag]))
						{
							unset($ex[$norm_n_tag]);
						}
						// otherwise we need to add a new relationship
						else
						{
							$n_tag = $this->get_tag($n_tag, false);
							$this->database->setQuery(sprintf($sql[0], $tid, $n_tag['id']));
							$this->database->execute();
						}
					}
				}
				// any tags that have not been unset were deleted on the form, so we need to reflect that in the database
				foreach ($ex as $e_tag=>$_v)
				{
					$e_tag = $this->get_tag($e_tag, false);
					$this->database->setQuery(sprintf($sql[1], $tid, $e_tag['id']));
					$this->database->execute();
				}
			}
		}

		$this->displayTask();
	}

	/**
	 * Delete a tag and its relationships
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		if (isset($_POST['tag']) && ($tid = (int)$_POST['tag']))
		{
			$tag = $this->get_tag($tid, false);
			if (isset($_POST['really']) && $_POST['really'] === 'on')
			{
				if (isset($_POST['do_merge']) && $_POST['do_merge'] === 'on')
				{
					if (!isset($_POST['merge_tag']))
					{
						JError::raiseError(404, 'Merge target not found');
					}
					$this->database->setQuery('SELECT id FROM #__tags WHERE tag = \'' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower(trim($_POST['merge_tag']))) . '\'');
					if (!($merge_id = $this->database->loadResult()))
					{
						JError::raiseError(404, 'Merge target not found');
					}
					$this->database->setQuery('UPDATE #__tags_object SET tagid = ' . $merge_id . ' WHERE tagid = ' . $tid);
					$this->database->execute();
				}
				else
				{
					$this->database->setQuery('DELETE FROM #__tags_object WHERE tagid = ' . $tid);
					$this->database->execute();
				}
				$this->database->setQuery('DELETE FROM #__tags WHERE id = ' . $tid);
				$this->database->execute();
			}
			else
			{
				$preload = $tag['raw_tag'];
			}
		}

		$this->displayTask();
	}

	/**
	 * Update focus areas
	 * 
	 * @return     void
	 */
	public function updatefocusareasTask()
	{
		$this->database->setQuery('SELECT id, tag_id, mandatory_depth, multiple_depth FROM #__focus_areas');

		$existing = $this->database->loadAssocList('id');

		// rebuilding from the form data is easier than finding and resolving differences
		$this->database->setQuery('TRUNCATE TABLE #__focus_area_resource_type_rel');
		$this->database->execute();
		foreach ($existing as $id => $fa)
		{
			// no form field == deleted
			if (!isset($_POST['name-' . $id]))
			{
				$this->database->setQuery('DELETE FROM #__focus_areas WHERE id = ' . $id);
				$this->database->execute();
				continue;
			}
			$new_tag = $this->get_tag($_POST['name-' . $id], false);
			$this->database->setQuery('UPDATE #__focus_areas SET 
				mandatory_depth = ' . ($_POST['mandatory-' . $id] === 'mandatory' ? 1 : ($_POST['mandatory-' . $id] === 'depth' ? (int)$_POST['mandatory-depth-' . $id] : 'NULL')) . ', 
				multiple_depth = ' . ($_POST['multiple-' . $id]  === 'multiple'  ? 1 : ($_POST['multiple-' . $id]  === 'depth' ? (int)$_POST['multiple-depth-' . $id]  : 'NULL')) . ', 
				tag_id = ' . $new_tag['id'].' 
				WHERE id = ' . $id
			);
			$this->database->execute();
			foreach ($_POST['types-'.$id] as $type_id)
			{
				$this->database->setQuery('INSERT INTO #__focus_area_resource_type_rel(focus_area_id, resource_type_id) VALUES (' . $id . ', ' . ((int)$type_id) . ')');
				$this->database->execute();
			}
		}

		for ($idx = 1; isset($_POST['name-new-' . $idx]); ++$idx)
		{
			if (!trim($_POST['name-new-' . $idx]))
			{
				continue;
			}
			$tag = $this->get_tag($_POST['name-new-' . $idx], false);

			$this->database->setQuery('INSERT INTO #__focus_areas(mandatory_depth, multiple_depth, tag_id) VALUES (' .
				($_POST['mandatory-new-' . $idx] === 'mandatory' ? 1 : ($_POST['mandatory-new-' . $idx] === 'depth' ? (int)$_POST['mandatory-depth-new-' . $idx] : 'NULL')) . ', ' .
				($_POST['multiple-new-' . $idx]  === 'multiple'  ? 1 : ($_POST['multiple-new-' . $idx]  === 'depth' ? (int)$_POST['multiple-depth-new-' . $idx]  : 'NULL')) . ', ' .
				$tag['id'] . ')' 
			);
			$this->database->execute();
			$id = $this->database->insertid();
			foreach ($_POST['types-new-' . $idx] as $type_id)
			{
				$this->database->setQuery('INSERT INTO #__focus_area_resource_type_rel(focus_area_id, resource_type_id) VALUES (' . $id . ', ' . ((int)$type_id) . ')');
				$this->database->execute();
			}
		}

		$this->metaTask();
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller
		);
	}

	/**
	 * Get data for a tag
	 *
	 * @return     void
	 */
	public function get_tag($tag_str, $detailed = true)
	{
		$this->database->setQuery(
			is_int($tag_str) 
				? 'SELECT DISTINCT t.id, tag, raw_tag, description, COUNT(to1.id) AS count FROM #__tags t LEFT JOIN #__tags_object to1 ON to1.tagid = t.id WHERE t.id = ' . $tag_str . ' GROUP BY t.id, tag, raw_tag, description'
				: 'SELECT DISTINCT t.id, tag, raw_tag, description, COUNT(to1.id) AS count FROM #__tags t LEFT JOIN #__tags_object to1 ON to1.tagid = t.id WHERE tag = ' . $this->database->quote($tag_str) . ' OR raw_tag = ' . $this->database->quote($tag_str) . ' GROUP BY t.id, tag, raw_tag, description'
		);
		if (($tag = $this->database->loadAssoc()))
		{
			$rv = array(
				'id' => $tag['id'],
				'tag' => $tag['tag'],
				'count' => $tag['count'],
				'raw_tag' => $tag['raw_tag'],
				'description' => $tag['description'],
				'new' => false 
			);
			if (!$detailed)
			{
				return $rv;
			}

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM #__tags_object to1  
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.objectid = ' . $tag['id']
			);
			$rv['labeled'] = $this->database->loadResultArray();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM #__tags_object to1  
				INNER JOIN #__tags t ON t.id = to1.objectid 
				WHERE to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.tagid = ' . $tag['id']
			);
			$rv['labels'] = $this->database->loadResultArray();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM #__tags_object to1  
				INNER JOIN #__tags t ON t.id = to1.tagid 
				WHERE to1.tbl = \'tags\' AND to1.label = \'parent\' AND to1.objectid = ' . $tag['id']
			);
			$rv['parents'] = $this->database->loadResultArray();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM #__tags_object to1  
				INNER JOIN #__tags t ON t.id = to1.objectid 
				WHERE to1.tbl = \'tags\' AND to1.label = \'parent\' AND to1.tagid = ' . $tag['id']
			);
			$rv['children'] = $this->database->loadResultArray();
			$rv['description'] = stripslashes($rv['description']);

			return $rv;
		}
		$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($tag_str));
		$this->database->setQuery('INSERT INTO #__tags(tag, raw_tag) VALUES(\'' . $norm_tag . '\', ' . $this->database->quote($tag_str) . ')');
		$this->database->execute();
		$id = $this->database->insertid();
		return array(
			'id'          => $id,
			'tag'         => $norm_tag,
			'raw_tag'     => $tag_str,
			'description' => '',
			'new'         => true,
			'labeled'     => array(),
			'labels'      => array(),
			'parents'     => array(),
			'children'    => array()
		);
	}
}

