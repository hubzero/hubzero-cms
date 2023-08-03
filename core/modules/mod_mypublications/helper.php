<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MyPublications;

use Hubzero\Module\Module;
use Components\Publications\Models\Orm\Publication;
use Components\Publications\Models\Orm\Version;
use Components\Publications\Models\Orm\Author;
use Components\Publications\Models\Orm\Category;
use Component;
use User;

/**
 * Module class for displaying a user's projects
 */
class Helper extends Module
{
	/**
	 * Display module content
	 * 
	 * @return  void
	 */
	public function display()
	{
		// Get the module parameters
		$this->moduleclass = $this->params->get('moduleclass', '');

		// Load classes
		include_once Component::path('com_publications') . '/models/orm/publication.php';
		include_once Component::path('com_publications') . '/tables/author.php';
		include_once Component::path('com_publications') . '/helpers/html.php';

		// Filters
		$filters = array(
			'created_by'        => User::get('id'),
			//'notauthorrole' => 'submitter',
			'sortby'        => 'created',
			//'usergroups'    => array(),
			'state'         => Version::STATE_DRAFT,
			//'access'        => array(0, 1, 3),
			'limitstart'    => 0,
			'limit'         => intval($this->params->get('limit', 5))
		);

		// Get results
		$query = self::allWithFilters($filters);

		$this->drafts = $query
			->limit($filters['limit'])
			->start($filters['limitstart'])
			->order($filters['sortby'], 'desc')
			->rows();

		$filters['state'] = Version::STATE_PUBLISHED;

		$query = self::allWithFilters($filters);

		$this->published = $query
			->limit($filters['limit'])
			->start($filters['limitstart'])
			->order($filters['sortby'], 'desc')
			->rows();

		$this->filters  = $filters;

		require $this->getLayoutPath();
	}

	/**
	 * Include needed libraries and push scripts and CSS to the document
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public static function allWithFilters($filters = array())
	{
		$query = Version::all();

		$r = $query->getTableName();
		$p = Publication::blank()->getTableName();
		$a = Author::blank()->getTableName();

		$query
			->select($r . '.*')
			->join($p, $p . '.id', $r . '.publication_id', 'inner');

		if (isset($filters['state']))
		{
			$query->whereIn($r . '.state', (array) $filters['state']);
		}

		if (isset($filters['type']))
		{
			if (!is_numeric($filters['type']))
			{
				$filters['type'] = Category::oneByAlias($filters['type'])->get('id');
			}
			$query->whereEquals($p . '.category', $filters['type']);
		}

		/*if (isset($filters['tag']) && $filters['tag'])
		{
			$database = App::get('db');
			$to = Components\Tags\Models\Objct::blank()->getTableName();
			$tg = Components\Tags\Models\Tag::blank()->getTableName();

			$cloud = new Components\Publications\Helpers\Tags($database);
			$tags = $cloud->parse($filters['tag']);

			$query->join($to, $to . '.objectid', $r . '.id');
			$query->join($tg, $tg . '.id', $to . '.tagid', 'inner');
			$query->whereEquals($to . '.tbl', 'publications');
			$query->whereIn($tg . '.tag', $tags);
		}*/

		if (isset($filters['search']))
		{
			$query->whereLike($r . '.title', $filters['search'], 1)
				->orWhereLike($r . '.description', $filters['search'], 1)
				->resetDepth();
		}

		if (isset($filters['created_by']))
		{
			$query->whereEquals($r . '.created_by', $filters['created_by']);
		}

		if (isset($filters['author']))
		{
			$query
				->join($a, $a . '.publication_version_id', $r . '.id', 'left')
				->whereEquals($a . '.user_id', $filters['author']);

			if (isset($filters['notauthorrole']))
			{
				$query->where($a . '.role', 'IS', null, 'and', 1)
					->orWhere($a . '.role', '!=', $filters['notauthorrole'], 1)
					->resetDepth();
			}
		}

		if (isset($filters['access']) && !empty($filters['access']))
		{
			if (!is_array($filters['access']) && !is_numeric($filters['access']))
			{
				switch ($filters['access'])
				{
					case 'public':
						$filters['access'] = 0;
						break;
					case 'protected':
						$filters['access'] = 3;
						break;
					case 'private':
						$filters['access'] = 4;
						break;
					case 'all':
					default:
						$filters['access'] = array(0, 1, 2, 3, 4);
						break;
				}
			}

			if (isset($filters['usergroups']) && !empty($filters['usergroups']))
			{
				$query->whereIn($r . '.access', (array) $filters['access'], 1)
					->orWhereIn($p . '.group_owner', (array) $filters['usergroups'], 1)
					->resetDepth();
			}
			else
			{
				$query->whereIn($r . '.access', (array) $filters['access']);
			}
		}
		elseif (isset($filters['usergroups']) && !empty($filters['usergroups']))
		{
			$query->whereIn($p . '.group_owner', (array) $filters['usergroups']);
		}

		if (isset($filters['now']))
		{
			$query->whereEquals($r . '.published_up', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.published_up', 'IS', null, 1)
				->orWhere($r . '.published_up', '<=', $filters['now'], 1)
				->resetDepth()
				->whereEquals($r . '.published_down', '0000-00-00 00:00:00', 1)
				->orWhere($r . '.published_down', 'IS', null, 1)
				->orWhere($r . '.published_down', '>=', $filters['now'], 1)
				->resetDepth();
		}

		if (isset($filters['startdate']) && $filters['startdate'])
		{
			$query->where($r . '.published_up', '>', $filters['startdate']);
		}
		if (isset($filters['enddate']) && $filters['enddate'])
		{
			$query->where($r . '.published_up', '<', $filters['enddate']);
		}

		return $query;
	}
}
