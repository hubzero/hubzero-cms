<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Admin\Controllers;

use Hubzero\Component\AdminController;
use Exception;
use Request;

/**
 * Tags controller class for managing raltionships between tags
 */
class Relationships extends AdminController
{
	/**
	 * Prelead
	 *
	 * @var string
	 */
	private $preload;

	/**
	 * Show a form for looking up a tag's relationships
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$tag = Request::getVar('tag', null);
		if ($tag && (int) $tag == $tag)
		{
			$this->database->setQuery('SELECT tag FROM `#__tags` WHERE id = ' . $tag);
			$this->view->set('preload', $this->database->loadResult());
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->display();
	}

	/**
	 * Show a form for managing focus areas
	 *
	 * @return  void
	 */
	public function metaTask()
	{
		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output the HTML
		$this->view
			->setLayout('meta')
			->display();
	}

	/**
	 * Implicit relationship lookup
	 * Generates data in JSON format
	 *
	 * @return  void
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
				'SELECT t.id, t.tag, t.raw_tag, count(t.id) AS count FROM `#__tags_object` to1
				LEFT JOIN `#__tags_object` to2 ON to2.tbl = to1.tbl AND to2.objectid = to1.objectid
				INNER JOIN `#__tags` t ON t.id = to2.tagid
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

			foreach ($follow as $idx => $tag_id)
			{
				$this->database->setQuery(
					'SELECT t.id, t.tag, t.raw_tag, count(t.id) AS count FROM `#__tags_object` to1
					LEFT JOIN `#__tags_object` to2 ON to2.tbl = to1.tbl AND to2.objectid = to1.objectid
					INNER JOIN `#__tags` t ON t.id = to2.tagid
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
				{
					$link['value'] /= $max_weight;
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
	 * Hierarchical relationship lookup
	 * Generates data in JSON format
	 *
	 * @return  void
	 */
	public function hierarchyTask()
	{
		static $DEPTH = 7;

		$tag = isset($_GET['tag']) ? $_GET['tag'] : 0;

		$links = array();
		$id = null;
		$descr = '';

		$rv = $tag = $this->get_tag($tag);
		$tag['type'] = 'center';
		$nodes = array(array(
			'id'      => $rv['id'],
			'tag'     => $rv['tag'],
			'raw_tag' => $rv['raw_tag']
		));
		$tagIdMap = array($rv['id'] => 0);
		$byDepth = array(array($tag));

		for ($depth = 0; $depth < $DEPTH; ++$depth)
		{
			if (!isset($byDepth[$depth]))
			{
				break;
			}

			foreach ($byDepth[$depth] as $tag)
			{
				$parents = 'SELECT DISTINCT t.id, t.tag, t.raw_tag, to1.label, \'in\' AS direction
					FROM `#__tags_object` to1
					INNER JOIN `#__tags` t ON t.id = to1.tagid
					WHERE to1.label IN (\'parent\', \'label\') AND to1.tbl = \'tags\' AND to1.objectid = ' . $tag['id'];
				$children = 'SELECT DISTINCT t.id, t.tag, t.raw_tag, to1.label, \'out\' AS direction
					FROM `#__tags_object` to1
					INNER JOIN `#__tags` t ON t.id = to1.objectid
					WHERE to1.label IN (\'parent\', \'label\') AND to1.tbl = \'tags\' AND to1.tagid = ' . $tag['id'];

				$this->database->setQuery(
					$tag['type'] == 'child' ? $children :
					($tag['type'] == 'parent' ? $parents : "$parents UNION $children")
				);

				foreach ($this->database->loadAssocList() as $subTag)
				{
					if (!array_key_exists($subTag['id'], $tagIdMap))
					{
						if ($subTag['direction'] == 'in' || $subTag['label'] != 'parent')
						{
							$subTag['type'] = $subTag['label'];
						}
						else if ($subTag['label'] == 'parent')
						{
							$subTag['type'] = 'child';
						}

						$nodes[] = $subTag;
						$tagIdMap[$subTag['id']] = count($nodes) - 1;
						if ($subTag['label'] == 'parent')
						{
							if (!array_key_exists($depth + 1, $byDepth))
							{
								$byDepth[$depth + 1] = array();
							}
							$byDepth[$depth + 1][] = $subTag;
						}
					}
					elseif ($subTag['label'] == 'parent')
					{

					}

					$links[] = array(
						'source' => $tagIdMap[$tag['id']],
						'target' => $tagIdMap[$subTag['id']]
					);
				}
			}
		}

		header('Content-type: text/plain');
		$rv['nodes'] = $nodes;
		$rv['links'] = $links;
		echo json_encode($rv, \JSON_PRETTY_PRINT);
		exit();
	}

	/**
	 * Tag suggester for autocompletion
	 * Generates data in JSON format
	 *
	 * @return  void
	 */
	public function suggestTask()
	{
		$suggestions = array();

		if (isset($_GET['term']))
		{
			$this->database->setQuery("SELECT raw_tag FROM `#__tags` WHERE raw_tag LIKE " . $this->database->quote('%' . $_GET['term'] . '%'));
			$later = array();
			foreach ($this->database->loadColumn() as $tag)
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
	 * @return  void
	 */
	public function updateTask()
	{
		if (isset($_POST['tag']) && ($tid = (int)$_POST['tag']))
		{
			$this->database->setQuery('UPDATE `#__tags` SET description = ' . $this->database->quote($_POST['description']) . ' WHERE id = ' . $tid);
			$this->database->execute();
			$tag = $this->get_tag($tid);
			$preload = $tag['raw_tag'];
			$normalize = create_function('$a', 'return preg_replace(\'/[^a-zA-Z0-9]/\', \'\', strtolower($a));');
			// reconcile post data with what we already know about a tag's relationships
			foreach (array(
				'labels'   => array(
					'INSERT INTO `#__tags_object` (tbl, label, tagid, objectid) VALUES (\'tags\', \'label\', %d, %d)',
					'DELETE FROM `#__tags_object` WHERE tbl = \'tags\' AND label = \'label\' AND tagid = %d AND objectid = %d'
				),
				'labeled'  => array(
					'INSERT INTO `#__tags_object` (tbl, label, objectid, tagid) VALUES (\'tags\', \'label\', %d, %d)',
					'DELETE FROM `#__tags_object` WHERE tbl = \'tags\' AND label = \'label\' AND objectid = %d AND tagid = %d'
				),
				'parents'  => array(
					'INSERT INTO `#__tags_object` (tbl, label, objectid, tagid) VALUES (\'tags\', \'parent\', %d, %d)',
					'DELETE FROM `#__tags_object` WHERE tbl = \'tags\' AND label = \'parent\' AND objectid = %d AND tagid = %d'
				),
				'children' => array(
					'INSERT INTO `#__tags_object` (tbl, label, tagid, objectid) VALUES (\'tags\', \'parent\', %d, %d)',
					'DELETE FROM `#__tags_object` WHERE tbl = \'tags\' AND label = \'parent\' AND tagid = %d AND objectid = %d'
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
				foreach ($ex as $e_tag => $_v)
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
						throw new Exception('Merge target not found', 404);
					}
					$this->database->setQuery('SELECT id FROM `#__tags` WHERE tag = \'' . preg_replace('/[^a-zA-Z0-9]/', '', strtolower(trim($_POST['merge_tag']))) . '\'');
					if (!($merge_id = $this->database->loadResult()))
					{
						throw new Exception('Merge target not found', 404);
					}
					$this->database->setQuery('UPDATE `#__tags_object` SET tagid = ' . $merge_id . ' WHERE tagid = ' . $tid);
					$this->database->execute();
				}
				else
				{
					$this->database->setQuery('DELETE FROM `#__tags_object` WHERE tagid = ' . $tid);
					$this->database->execute();
				}
				$this->database->setQuery('DELETE FROM `#__tags` WHERE id = ' . $tid);
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
	 * @return  void
	 */
	public function updatefocusareasTask()
	{
		$this->database->setQuery('SELECT id, tag_id, mandatory_depth, multiple_depth FROM `#__focus_areas`');

		$existing = $this->database->loadAssocList('id');

		// rebuilding from the form data is easier than finding and resolving differences
		$this->database->setQuery('TRUNCATE TABLE #__focus_area_resource_type_rel');
		$this->database->execute();
		error_log(var_export($_POST, 1));
		foreach ($existing as $id => $fa)
		{
			// no form field == deleted
			if (!isset($_POST['name-' . $id]))
			{
				$this->database->setQuery('DELETE FROM `#__focus_areas` WHERE id = ' . $id);
				$this->database->execute();
				continue;
			}
			$new_tag = $this->get_tag($_POST['name-' . $id], false);
			$this->database->setQuery('UPDATE `#__focus_areas` SET
				mandatory_depth = ' . ($_POST['mandatory-' . $id] === 'mandatory' ? 1 : ($_POST['mandatory-' . $id] === 'depth' ? (int)$_POST['mandatory-depth-' . $id] : 'NULL')) . ',
				multiple_depth = ' . ($_POST['multiple-' . $id]  === 'multiple'  ? 1 : ($_POST['multiple-' . $id]  === 'depth' ? (int)$_POST['multiple-depth-' . $id]  : 'NULL')) . ',
				tag_id = ' . $new_tag['id'].'
				WHERE id = ' . $id
			);
			$this->database->execute();
			if (isset($_POST['types-'.$id]))
			{
				foreach ($_POST['types-'.$id] as $type_id)
				{
					$this->database->setQuery('INSERT INTO `#__focus_area_resource_type_rel` (focus_area_id, resource_type_id) VALUES (' . $id . ', ' . ((int)$type_id) . ')');
					$this->database->execute();
				}
			}
		}

		for ($idx = 1; isset($_POST['name-new-' . $idx]); ++$idx)
		{
			if (!trim($_POST['name-new-' . $idx]))
			{
				continue;
			}
			$tag = $this->get_tag($_POST['name-new-' . $idx], false);

			$this->database->setQuery('INSERT INTO `#__focus_areas` (mandatory_depth, multiple_depth, tag_id) VALUES (' .
				($_POST['mandatory-new-' . $idx] === 'mandatory' ? 1 : ($_POST['mandatory-new-' . $idx] === 'depth' ? (int)$_POST['mandatory-depth-new-' . $idx] : 'NULL')) . ', ' .
				($_POST['multiple-new-' . $idx]  === 'multiple'  ? 1 : ($_POST['multiple-new-' . $idx]  === 'depth' ? (int)$_POST['multiple-depth-new-' . $idx]  : 'NULL')) . ', ' .
				$tag['id'] . ')'
			);
			$this->database->execute();
			$id = $this->database->insertid();
			if (isset($_POST['types-new-' . $idx]))
			{
				foreach ($_POST['types-new-' . $idx] as $type_id)
				{
					$this->database->setQuery('INSERT INTO `#__focus_area_resource_type_rel` (focus_area_id, resource_type_id) VALUES (' . $id . ', ' . ((int)$type_id) . ')');
					$this->database->execute();
				}
			}
		}

		$this->metaTask();
	}

	/**
	 * Get data for a tag
	 *
	 * @return  void
	 */
	public function get_tag($tag_str, $detailed = true)
	{
		$this->database->setQuery(
			is_int($tag_str)
				? 'SELECT DISTINCT t.id, tag, raw_tag, description, COUNT(to1.id) AS count FROM `#__tags` t LEFT JOIN `#__tags_object` to1 ON to1.tagid = t.id WHERE t.id = ' . $tag_str . ' GROUP BY t.id, tag, raw_tag, description'
				: 'SELECT DISTINCT t.id, tag, raw_tag, description, COUNT(to1.id) AS count FROM `#__tags` t LEFT JOIN `#__tags_object` to1 ON to1.tagid = t.id WHERE tag = ' . $this->database->quote($tag_str) . ' OR raw_tag = ' . $this->database->quote($tag_str) . ' GROUP BY t.id, tag, raw_tag, description'
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
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.tagid
				WHERE to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.objectid = ' . $tag['id']
			);
			$rv['labeled'] = $this->database->loadColumn();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.objectid
				WHERE to1.tbl = \'tags\' AND to1.label = \'label\' AND to1.tagid = ' . $tag['id']
			);
			$rv['labels'] = $this->database->loadColumn();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.tagid
				WHERE to1.tbl = \'tags\' AND to1.label = \'parent\' AND to1.objectid = ' . $tag['id']
			);
			$rv['parents'] = $this->database->loadColumn();

			$this->database->setQuery(
				'SELECT DISTINCT t.raw_tag
				FROM `#__tags_object` to1
				INNER JOIN `#__tags` t ON t.id = to1.objectid
				WHERE to1.tbl = \'tags\' AND to1.label = \'parent\' AND to1.tagid = ' . $tag['id']
			);
			$rv['children'] = $this->database->loadColumn();
			$rv['description'] = stripslashes($rv['description']);

			return $rv;
		}

		$norm_tag = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($tag_str));
		$this->database->setQuery('INSERT INTO `#__tags` (tag, raw_tag) VALUES (' . $this->database->quote($norm_tag) . ', ' . $this->database->quote($tag_str) . ')');
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
