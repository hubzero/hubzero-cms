<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Tags plugin class for forum entries
 */
class plgTagsForum extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Get the group IDs for all the groups of a specific user
	 *
	 * @param   integer  $uid  User ID
	 * @return  array
	 */
	private function _getGroupIds($uid=0)
	{
		$dbh = App::get('db');
		$dbh->setQuery(
			'SELECT DISTINCT gidNumber FROM `#__xgroups_members` WHERE uidNumber=' . $uid
		);
		return $dbh->loadColumn();
	}

	/**
	 * Retrieve records for items tagged with specific tags
	 * 
	 * @param   array    $tags        Tags to match records against
	 * @param   mixed    $limit       SQL record limit
	 * @param   integer  $limitstart  SQL record limit start
	 * @param   string   $sort        The field to sort records by
	 * @param   mixed    $areas       An array or string of areas that should retrieve records
	 * @return  mixed    Returns integer when counting records, array when retrieving records
	 */
	public function onTagView($tags, $limit=0, $limitstart=0, $sort='', $areas=null)
	{
		$response = array(
			'name'    => $this->_name,
			'title'   => Lang::txt('PLG_TAGS_FORUM'),
			'total'   => 0,
			'results' => null,
			'sql'     => ''
		);

		$database = App::get('db');

		$ids = array();
		foreach ($tags as $tag)
		{
			$ids[] = $tag->get('id');
		}
		$ids = implode(',', $ids);

		$addtl_where = array();

		$gids = $this->_getGroupIds((int)User::get('id', 0));

		if (!User::authorise('core.view', 'com_forum'))
		{
			$addtl_where[] = 'e.scope_id IN (0' . ($gids ? ',' . join(',', $gids) : '') . ')';
		}
		else
		{
			$viewlevels	= '0,' . implode(',', User::getAuthorisedViewLevels());

			if ($gids)
			{
				$addtl_where[] = '(e.access IN (' . $viewlevels . ') OR ((e.access = 4 OR e.access = 5) AND e.scope_id IN (0,' . join(',', $gids) . ')))';
			}
			else
			{
				$addtl_where[] = '(e.access IN (' . $viewlevels . '))';
			}
		}

		// Build the query
		$e_count = "SELECT COUNT(f.id) FROM (SELECT e.id, COUNT(DISTINCT t.tagid) AS uniques";
		$e_fields = "SELECT e.id, e.title, e.id AS alias, e.comment AS itext, e.comment AS ftext, e.state, e.created, e.created_by, e.modified, e.created AS publish_up, NULL AS publish_down,
					(CASE WHEN e.scope_id > 0 AND e.scope='group' THEN
						concat('/groups/', g.cn, concat('/forum/', coalesce(concat(s.alias, '/', coalesce(concat(c.alias, '/'), ''))), CASE WHEN e.parent > 0 THEN e.parent ELSE e.id END))
					ELSE
						concat('/forum/', coalesce(concat(s.alias, '/', coalesce(concat(c.alias, '/'), ''))), CASE WHEN e.parent > 0 THEN e.parent ELSE e.id END)
					END) AS href,
					'forum' AS section, COUNT(DISTINCT t.tagid) AS uniques, CONCAT(e.thread, ':', e.parent) AS params, e.scope AS rcount, c.alias AS data1, s.alias AS data2, e.scope_id AS data3 "; //e.last_activity AS rcount, c.alias AS data1, s.alias AS data2, g.cn AS data3 
		$e_from  = " FROM #__forum_posts AS e
		 			LEFT JOIN #__forum_categories c ON c.id = e.category_id
					LEFT JOIN #__forum_sections s ON s.id = c.section_id
					LEFT JOIN #__xgroups g ON g.gidNumber = e.scope_id
					LEFT JOIN #__tags_object AS t ON t.objectid=e.id AND t.tbl='forum' AND t.tagid IN ($ids)";
		$e_where  = " WHERE e.state=1 AND e.parent=0" . ($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '');
		$e_where .= " GROUP BY e.id HAVING uniques=" . count($tags);
		$order_by  = " ORDER BY ";
		switch ($sort)
		{
			case 'title':
				$order_by .= 'title ASC, created';
				break;
			case 'id':
				$order_by .= "id DESC";
				break;
			case 'date':
			default:
				$order_by .= 'created DESC, title';
				break;
		}
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		$database->setQuery($e_count . $e_from . $e_where . ") AS f");
		$response['total'] = $database->loadResult();

		if ($areas && $areas == $response['name'])
		{
			$database->setQuery($e_fields . $e_from . $e_where . $order_by);
			$response['results'] = $database->loadObjectList();
		}
		else
		{
			$response['sql'] = $e_fields . $e_from . $e_where;
		}

		return $response;
	}

	/**
	 * Static method for formatting results
	 *
	 * @param   object  $row  Database row
	 * @return  string  HTML
	 */
	public static function out($row)
	{
		include_once(Component::path('com_forum') . DS . 'models' . DS . 'post.php');

		$row->scope    = $row->rcount;
		$row->scope_id = $row->data3;
		$row->section  = $row->data2;
		$row->category = $row->data1;

		$p = explode(':', $row->params);

		$row->thread   = $p[0];
		$row->parent   = $p[1];
		$row->comment  = $row->ftext;

		$view = new \Hubzero\Plugin\View(array(
			'folder'  => 'tags',
			'element' => 'forum',
			'name'    => 'result'
		));
		$view->post = \Components\Forum\Models\Post::blank()->set(array(
			'id'         => $row->id,
			'title'      => $row->title,
			'alias'      => $row->alias,
			'comment'    => $row->comment,
			'state'      => $row->state,
			'created'    => $row->created,
			'created_by' => $row->created_by,
			'scope'      => $row->rcount,
			'scope_id'   => $row->data3,
			'thread'     => $row->thread,
			'parent'     => $row->parent,
			'section'    => $row->data2,
			'category'   => $row->data1
		));

		return $view->loadTemplate();
	}
}
