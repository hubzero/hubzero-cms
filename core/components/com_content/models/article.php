<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Models;

use Components\Categories\Models\Category;
use Hubzero\Database\Relational;
use Hubzero\Database\Asset;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use stdClass;
use Component;
use Lang;
use User;
use Date;

require_once __DIR__ . '/frontpage.php';
require_once __DIR__ . '/rating.php';
require_once Component::path('com_categories') . '/models/category.php';

/**
 * Model class for an article
 */
class Article extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * Database state constants
	 **/
	const STATE_ARCHIVED = 2;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'content';

	/**
	 * The table name
	 *
	 * @var  string
	 */
	protected $table = '#__content';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'published_up';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'     => 'notempty',
		'introtext' => 'notempty',
		'scope'     => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'publish_up',
		'publish_down',
		'fulltext',
		'images',
		'urls',
		'modified',
		'modified_by',
		'metadata',
		'attribs',
		'asset_id'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'ordering'
	);

	/**
	 * Rules array converted into JSON string.
	 *
	 * @var string
	 */
	public $assetRules;

	/**
	 * Registry params object 
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Registry params object 
	 *
	 * @var  object
	 */
	protected $metadataRegistry = null;

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('publish_down', function($data)
		{
			if (!$data['publish_down'] || $data['publish_down'] == '0000-00-00 00:00:00')
			{
				return false;
			}
			return $data['publish_down'] >= $data['publish_up'] ? false : Lang::txt('The entry cannot end before it begins');
		});
	}

	/**
	 * Generates automatic asset_id field value
	 *
	 * @return  integer
	 */
	public function automaticAssetId()
	{
		if (!empty($this->assetRules))
		{
			return parent::automaticAssetId();
		}
		return $this->get('asset_id');
	}

	/**
	 * Generates automatic fulltext field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticFulltext($data)
	{
		if (!isset($data['fulltext']) || is_null($data['fulltext']))
		{
			$data['fulltext'] = '';
		}

		return $data['fulltext'];
	}

	/**
	 * Generates automatic images field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticImages($data)
	{
		if (!isset($data['images']) || is_null($data['images']))
		{
			$data['images'] = '';
		}

		return $data['images'];
	}

	/**
	 * Generates automatic urls field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticUrls($data)
	{
		if (!isset($data['urls']) || is_null($data['urls']))
		{
			$data['urls'] = '';
		}

		return $data['urls'];
	}

	/**
	 * Establish relationship to featured
	 *
	 * @return  object
	 */
	public function frontpage()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Frontpage', 'content_id');
	}

	/**
	 * Is this a frontpage article?
	 *
	 * @return  bool
	 */
	public function isFrontpage()
	{
		return ($this->frontpage()->row()->get('content_id', 0) > 0);
	}

	/**
	 * Is this a featured article?
	 *
	 * @return  bool
	 */
	public function isFeatured()
	{
		return ($this->get('featured', 0) > 0);
	}

	/**
	 * Establish relationship to featured
	 *
	 * @return  object
	 */
	public function rating()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Rating', 'content_id');
	}

	/**
	 * Establish relationship to category
	 *
	 * @return  object
	 */
	public function category()
	{
		return $this->belongsToOne('\Components\Categories\Models\Category', 'catid');
	}

	/**
	 * Establish relationship to author
	 *
	 * @return  object
	 */
	public function author()
	{
		return $this->belongsToOne('\Hubzero\User\User', 'created_by');
	}

	/**
	 * Establish relationship to editor
	 *
	 * @return  object
	 */
	public function editor()
	{
		return $this->belongsToOne('\Hubzero\User\User', 'checked_out');
	}

	/**
	 * Get a list of all categories
	 *
	 * @return  object
	 */
	public function categories()
	{
		$categories = Category::all()
			->whereEquals('extension', 'com_content')
			->whereIn('published', array(0, 1))
			->order('lft', 'asc');
		return $categories;
	}

	/**
	 * Display state as human readable text
	 *
	 * @return  string
	 */
	public function transformState()
	{
		$states = array(
			'0' => 'Unpublished',
			'1' => 'Published',
			'2' => 'Archived',
			'-2' => 'Trashed'
		);
		$stateNum = $this->get('state', 0);
		return $states[$stateNum];
	}

	/**
	 * Establish relationship to access level
	 *
	 * @return  object
	 */
	public function accessLevel()
	{
		return $this->belongsToOne('\Hubzero\Access\Viewlevel', 'access');
	}

	/**
	 * Establish relationship to asset
	 *
	 * @return  object
	 */
	public function asset()
	{
		return $this->belongsToOne('\Hubzero\Access\Asset', 'asset_id');
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishUp($data)
	{
		if (!isset($data['publish_up']))
		{
			$data['publish_up'] = null;
		}

		$publish_up = $data['publish_up'];

		if (!$publish_up || $publish_up == '0000-00-00 00:00:00')
		{
			$publish_up = $this->isNew() ? Date::toSql() : $this->created;
		}

		return $publish_up;
	}

	/**
	 * Generates automatic publish_down field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticPublishDown($data)
	{
		if (!isset($data['publish_down']) || !$data['publish_down'])
		{
			$data['publish_down'] = null;
		}
		return $data['publish_down'];
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		$data['modified'] = Date::of()->toSql();
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedBy($data)
	{
		$data['modified_by'] = User::getInstance()->get('id');
		return $data['modified_by'];
	}

	/**
	 * Generates automatic ordering field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticOrdering($data)
	{
		if (empty($data['ordering']) && !empty($data['catid']))
		{
			$lastOrderedRow = self::all()
				->whereEquals('catid', $data['catid'])
				->order('ordering', 'desc')
				->row();
			$lastOrderNum = $lastOrderedRow->get('ordering', 0);
			$data['ordering'] = $lastOrderNum + 1;
		}
		return $data['ordering'];
	}

	/**
	 * Get params as Registry object
	 *
	 * @return  object
	 */
	public function transformAttribs()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$itemRegistry = new Registry($this->get('attribs'));

			$componentRegistry = Component::params('com_content');
			$componentRegistry->merge($itemRegistry);

			$this->paramsRegistry = $componentRegistry;
		}

		return $this->paramsRegistry;
	}

	/**
	 * Transform title
	 *
	 * @return  string
	 */
	public function transformName()
	{
		return $this->get('title');
	}

	/**
	 * Get metadata as an object
	 *
	 * @return  object
	 */
	public function transformMetadata()
	{
		if (!($this->metadataRegistry instanceof Registry))
		{
			$this->metadataRegistry = new Registry($this->get('metadata'));
		}

		return $this->metadataRegistry;
	}

	/**
	 * Generates automatic attribs field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAttribs($data)
	{
		if (!isset($data['attribs']))
		{
			$data['attribs'] = '';
		}

		if (!empty($data['attribs']))
		{
			$metadata = new Registry($data['attribs']);
			$data['attribs'] = $metadata->toString();
		}

		return $data['attribs'];
	}

	/**
	 * Generates automatic metadata field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticMetadata($data)
	{
		if (!isset($data['metadata']))
		{
			$data['metadata'] = '';
		}

		if (!empty($data['metadata']))
		{
			$metadata = new Registry($data['metadata']);
			$data['metadata'] = $metadata->toString();
		}

		return $data['metadata'];
	}

	/**
	 * Build a Form object and bind data to it
	 *
	 * @param   string  $client
	 * @return  object
	 */
	public function getForm($client = '')
	{
		$file = __DIR__ . '/forms/article' . ($client ? '_' . $client : ''). '.xml';
		$file = \Filesystem::cleanPath($file);

		$form = new Form('content', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->getAttributes();
		$data['attribs']  = $this->attribs->toArray();
		$data['metadata'] = $this->metadata->toArray();
		if ($this->isNew())
		{
			unset($data['asset_id']);
		}

		$form->bind($data);

		return $form;
	}

	/**
	 * Save ordering
	 *
	 * @param   array  $ordering
	 * @return  bool
	 */
	public static function saveorder($ordering)
	{
		if (empty($ordering) || !is_array($ordering))
		{
			return false;
		}
		foreach ($ordering as $catid => $order)
		{
			$existingOrderedRows = self::all()
				->whereEquals('catid', $catid)
				->order('ordering', 'asc')
				->rows();
			if (count($existingOrderedRows) <= 1)
			{
				continue;
			}
			$existingOrderIds = array();
			foreach ($existingOrderedRows as $row)
			{
				$pkValue = $row->get('id');
				$existingOrderIds[$pkValue] = $row->ordering;
			}
			$newOrder = $order + $existingOrderIds;
			if ($newOrder != $existingOrderIds)
			{
				asort($newOrder);
				$iterator = 1;
				foreach ($newOrder as $pk => $orderValue)
				{
					$existingOrderedRows->seek($pk)->set('ordering', $iterator);
					$iterator++;
				}
				if (!$existingOrderedRows->save())
				{
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Move a single item up or down in ordering
	 *
	 * @param   integer  $delta
	 * @param   string   $where
	 * @return  bool
	 */
	public function move($delta, $where = '')
	{
		// If the change is none, do nothing.
		if (empty($delta))
		{
			return true;
		}

		// Select the primary key and ordering values from the table.
		$query = self::all()
			->whereEquals('catid', $this->get('catid'));

		// If the movement delta is negative move the row up.
		if ($delta < 0)
		{
			$query->where('ordering', '<', (int) $this->get('ordering'));
			$query->order('ordering', 'desc');
		}
		// If the movement delta is positive move the row down.
		elseif ($delta > 0)
		{
			$query->where('ordering', '>', (int) $this->get('ordering'));
			$query->order('ordering', 'asc');
		}

		// Add the custom WHERE clause if set.
		if ($where)
		{
			$query->whereRaw($where);
		}

		// Select the first row with the criteria.
		$row = $query->row();

		// If a row is found, move the item.
		if ($row->get($this->pk))
		{
			$prev = $this->get('ordering');

			// Update the ordering field for this instance to the row's ordering value.
			$this->set('ordering', (int) $row->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}

			// Update the ordering field for the row to this instance's ordering value.
			$row->set('ordering', (int) $prev);

			// Check for a database error.
			if (!$row->save())
			{
				return false;
			}
		}
		else
		{
			// Update the ordering field for this instance.
			$this->set('ordering', (int) $this->get('ordering'));

			// Check for a database error.
			if (!$this->save())
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Get total number of records that will be indexed by Solr.
	 *
	 * @return integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}

	/**
	 * Get records to be included in solr index
	 *
	 * @param   integer  $limit
	 * @param   integer  $offset
	 * @return  object   Hubzero\Database\Rows
	 */
	public static function searchResults($limit, $offset = 0)
	{
		return self::all()->start($offset)->limit($limit)->rows();
	}

	/**
	 * Namespace used for solr Search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		$searchNamespace = 'content';
		return $searchNamespace;
	}

	/**
	 * Generate solr search Id
	 *
	 * @return  string
	 */
	public function searchId()
	{
		$searchId = self::searchNamespace() . '-' . $this->id;
		return $searchId;
	}

	/**
	 * Generate search document for Solr
	 *
	 * @return  array
	 */
	public function searchResult()
	{
		$page = new stdClass;
		$path = $this->category->path;

		if (strpos($path, 'uncategorized') === false && strpos($path, 'uncategorised') === false)
		{
			$url = $path . '/' . $this->alias;
		}
		else
		{
			$url = $this->alias;
		}

		if ($this->get('state') == 1 && $this->get('access') == 1)
		{
			$access_level = 'public';
		}
		// Registered condition
		elseif ($this->get('state') == 1 && $this->get('access') == 2)
		{
			$access_level = 'registered';
		}
		// Default private
		else
		{
			$access_level = 'private';
		}

		$page->url = \Request::root() . \Route::urlForClient('site', $url);
		$page->access_level = $access_level;
		$page->owner_type = 'user';
		$page->owner = $this->created_by;
		$page->id = $this->searchId();
		$page->title = $this->title;
		$page->hubtype = self::searchNamespace();
		$page->description = \Hubzero\Utility\Sanitize::stripAll($this->introtext);
		$page->fulltext = $this->introtext . ' ' . $this->fulltext;
		return $page;
	}

	/**
	 * Increment the hit counter
	 *
	 * @return  void
	 */
	public function hit()
	{
		$this->set('hits', (int)$this->get('hits') + 1);
		$this->save();
	}

	/**
	 * Build a query based on common filters
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public static function allByFilters($filters = array())
	{
		$query = self::all()->setTableAlias('a')->newQuery(); //self::blank()->getQuery(); //self::all();

		$query
			->select('a.id')
			->select('a.title')
			->select('a.alias')
			->select('a.title_alias')
			->select('a.introtext')
			->select('a.language')
			->select('a.checked_out')
			->select('a.checked_out_time')
			->select('a.catid')
			->select('a.created')
			->select('a.created_by')
			->select('a.created_by_alias')
			->select('CASE WHEN a.modified IS NULL THEN a.created ELSE a.modified END', 'modified')
			->select('a.modified_by')
			->select('uam.name', 'modified_by_name')
			->select('CASE WHEN a.publish_up IS NULL THEN a.created ELSE a.publish_up END', 'publish_up')
			->select('a.publish_down')
			->select('a.images')
			->select('a.urls')
			->select('a.attribs')
			->select('a.metadata')
			->select('a.metakey')
			->select('a.metadesc')
			->select('a.access')
			->select('a.hits')
			->select('a.featured')
			->select('a.xreference')
			->select('a.fulltext', 'readmore');
			//->from(self::blank()->getTableName(), 'a');

		if (isset($filters['published']) && !is_array($filters['published']))
		{
			$filters['published'] = array($filters['published']);
		}

		// Process an Archived Article layout
		if (isset($filters['published']) && in_array(self::STATE_ARCHIVED, $filters['published']))
		{
			// If badcats is not null, this means that the article is inside an archived category
			// In this case, the state is set to 2 to indicate Archived (even if the article state is Published)
			$query->select('CASE WHEN badcats.id is null THEN a.state ELSE 2 END', 'state');
		}
		else
		{
			// Process non-archived layout
			// If badcats is not null, this means that the article is inside an unpublished category
			// In this case, the state is set to 0 to indicate Unpublished (even if the article state is Published)
			$query->select('CASE WHEN badcats.id is not null THEN 0 ELSE a.state END', 'state');
		}

		// Join over the frontpage articles.
		if ($filters['context'] != 'com_content.featured')
		{
			$query->join('#__content_frontpage AS fp', 'fp.content_id', 'a.id', 'left');
		}

		// Join on category table.
		$query->select('c.title', 'category_title')
			->select('c.path', 'category_route')
			->select('c.access', 'category_access')
			->select('c.alias', 'category_alias');
		$query->join('#__categories AS c', 'c.id', 'a.catid', 'left');

		// Join over the users for the author and modified_by names.
		$query->select("CASE WHEN a.created_by_alias > ' ' THEN a.created_by_alias ELSE ua.name END", 'author');
		$query->select('ua.email', 'author_email');

		$query->join('#__users AS ua', 'ua.id', 'a.created_by', 'left');
		$query->join('#__users AS uam', 'uam.id', 'a.modified_by', 'left');

		// Filter by language
		if (isset($filters['language']) && $filters['language'])
		{
			$query->whereIn('a.language', array(Lang::getTag(), '*'));
		}

		// Join over the categories to get parent category titles
		$query->select('parent.title', 'parent_title')
			->select('parent.id', 'parent_id')
			->select('parent.path', 'parent_route')
			->select('parent.alias', 'parent_alias');
		$query->join('#__categories as parent', 'parent.id', 'c.parent_id', 'left');

		// Join on voting table
		$query->select('ROUND(v.rating_sum / v.rating_count, 0)', 'rating')
			->select('v.rating_count', 'rating_count');
		$query->join('#__content_rating AS v', 'a.id', 'v.content_id', 'left');

		// Join to check for category published state in parent categories up the tree
		// If all categories are published, badcats.id will be null, and we just use the article state
		$query->select('c.published');
		$query->select('CASE WHEN badcats.id is null THEN c.published ELSE 0 END', 'parents_published');
		$subquery  = " (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ";
		$subquery .= "ON cat.lft BETWEEN parent.lft AND parent.rgt ";
		$subquery .= "WHERE parent.extension = 'com_content'";
		if (isset($filters['published']) && in_array(self::STATE_ARCHIVED, $filters['published']))
		{
			// Find any up-path categories that are archived
			// If any up-path categories are archived, include all children in archived layout
			$subquery .= ' AND parent.published = 2 GROUP BY cat.id)';
			// Set effective state to archived if up-path category is archived
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 2 END';
		}
		else
		{
			// Find any up-path categories that are not published
			// If all categories are published, badcats.id will be null, and we just use the article state
			$subquery .= ' AND parent.published != 1 GROUP BY cat.id)';
			// Select state to unpublished if up-path category is unpublished
			$publishedWhere = 'CASE WHEN badcats.id is null THEN a.state ELSE 0 END';
		}
		$query->join($subquery . ' AS badcats', 'badcats.id', 'c.id', 'left outer');

		// Filter by access level.
		if (isset($filters['access']))
		{
			if (!is_array($filters['access']))
			{
				$filters['access'] = array($filters['access']);
			}
			$query->whereIn('a.access', $filters['access']);
			$query->whereIn('c.access', $filters['access']);
		}

		// Filter by published state.
		if (isset($filters['published']) && $publishedWhere)
		{
			if (!is_array($filters['published']))
			{
				$filters['published'] = array($filters['published']);
			}
			$query->whereRaw($publishedWhere . ' IN (' . implode(',', $filters['published']) . ')');
		}

		// Filter by featured state
		if (isset($filters['featured']))
		{
			switch ($filters['featured'])
			{
				case 'hide':
					$query->whereEquals('a.featured', 0);
					break;

				case 'only':
					$query->whereEquals('a.featured', 1);
					break;

				case 'show':
				default:
					// Normally we do not discriminate
					// between featured/unfeatured items.
					break;
			}
		}

		// Filter by a single or group of articles.
		if (isset($filters['article_id']))
		{
			$articleId = $filters['article_id'];

			if (!isset($filters['article_id.include']))
			{
				$filters['article_id.include'] = true;
			}

			if (is_numeric($articleId))
			{
				$type = $filters['article_id.include'] ? '=' : '<>';
				$query->where('a.id', $type, (int) $articleId);
			}
			elseif (is_array($articleId))
			{
				\Hubzero\Utility\Arr::toInteger($articleId);
				$articleId = implode(',', $articleId);
				$type = $filters['article_id.include'] ? 'IN' : 'NOT IN';
				$query->whereRaw('a.id', $type . ' (' . $articleId . ')');
			}
		}

		// Filter by a single or group of categories
		if (isset($filters['category_id']))
		{
			$categoryId = $filters['category_id'];

			if (!isset($filters['category_id.include']))
			{
				$filters['category_id.include'] = true;
			}

			if (is_numeric($categoryId))
			{
				if (!isset($filters['category_id.include']))
				{
					$filters['category_id.include'] = true;
				}

				$type = $filters['category_id.include'] ? '=' : '<>';

				if (!isset($filters['subcategories']))
				{
					$filters['subcategories'] = false;
				}

				// Add subcategory check
				$includeSubcategories = $filters['subcategories'];

				if ($includeSubcategories)
				{
					if (!isset($filters['max_category_levels']))
					{
						$filters['max_category_levels'] = 1;
					}
					$levels = (int) $filters['max_category_levels'];

					// Create a subquery for the subcategory list
					$subQuery = \App::get('db')->getQuery();
					$subQuery->select('sub.id');
					$subQuery->from('#__categories', 'sub');
					$subQuery->joinRaw('#__categories as this', 'sub.lft > this.lft AND sub.rgt < this.rgt', 'inner');
					$subQuery->whereEquals('this.id', (int) $categoryId);
					if ($levels >= 0)
					{
						$subQuery->whereRaw('sub.level <= this.level + ' . $levels);
					}

					// Add the subquery to the main query
					$query->where('a.catid', $type, (int) $categoryId, 'and', 1)
						->orWhereRaw('a.catid IN (' . $subQuery->toString() . ')', array(), 1)
						->resetDepth();
				}
				else
				{
					$query->where('a.catid', $type, (int) $categoryId);
				}
			}
			elseif (is_array($categoryId) && (count($categoryId) > 0))
			{
				\Hubzero\Utility\Arr::toInteger($categoryId);
				$categoryId = implode(',', $categoryId);
				if (!empty($categoryId))
				{
					$type = $filters['category_id.include'] ? 'IN' : 'NOT IN';
					$query->whereRaw('a.catid', $type . ' (' . $categoryId . ')');
				}
			}
		}

		// Filter by author
		$authorWhere = '';
		if (isset($filters['author_id']))
		{
			$authorId = $filters['author_id'];
			//$authorWhere = '';

			if (!isset($filters['author_id.include']))
			{
				$filters['author_id.include'] = true;
			}

			if (is_numeric($authorId))
			{
				$type = $filters['author_id.include'] ? '=' : '<>';
				//$authorWhere = 'a.created_by '.$type.(int) $authorId;
				$authorWhere = array('a.created_by', $type, (int) $authorId);
			}
			elseif (is_array($authorId))
			{
				\Hubzero\Utility\Arr::toInteger($authorId);
				$authorId = implode(',', $authorId);

				if ($authorId)
				{
					$type = $filters['author_id.include'] ? 'IN' : 'NOT IN';
					//$authorWhere = 'a.created_by '.$type.' ('.$authorId.')';

					$authorWhere = array('a.created_by', $type, '(' . $authorId . ')');
				}
			}
		}

		// Filter by author alias
		$authorAliasWhere = '';
		if (isset($filters['author_alias']))
		{
			$authorAlias = $filters['author_alias'];

			if (!isset($filters['author_alias.include']))
			{
				$filters['author_alias.include'] = true;
			}

			if (is_string($authorAlias))
			{
				$type = $filters['author_alias.include'] ? '=' : '<>';

				$authorAliasWhere = array('a.created_by_alias', $type, $authorAlias);
			}
			elseif (is_array($authorAlias))
			{
				$first = current($authorAlias);

				if (!empty($first))
				{
					\Hubzero\Utility\Arr::toString($authorAlias);

					foreach ($authorAlias as $key => $alias)
					{
						$authorAlias[$key] = $db->Quote($alias);
					}

					$authorAlias = implode(',', $authorAlias);

					if ($authorAlias)
					{
						$type = $filters['author_alias.include'] ? 'IN' : 'NOT IN';

						$authorAliasWhere = array('a.created_by_alias', $type, '(' . $authorAlias . ')');
					}
				}
			}
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere))
		{
			$query->where($authorWhere[0], $authorWhere[1], $authorWhere[2], 1)
				->orWhere($authorAliasWhere[0], $authorAliasWhere[1], $authorAliasWhere[2], 1)
				->resetDepth();
		}
		elseif (empty($authorWhere) && empty($authorAliasWhere))
		{
			// If both are empty we don't want to add to the query
		}
		elseif (!empty($authorWhere))
		{
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere[0], $authorWhere[1], $authorWhere[2]);
		}
		elseif (!empty($authorAliasWhere))
		{
			// One of these is empty, the other is not so we just add both
			$query->where($authorAliasWhere[0], $authorAliasWhere[1], $authorAliasWhere[2]);
		}

		// Filter by start and end dates.
		$nullDate = null;
		$nowDate = Date::of('now')->toSql();

		if (!User::authorise('core.edit.state', 'com_content')
		 && !User::authorise('core.edit', 'com_content'))
		{
			$query->where('a.publish_up', 'IS', $nullDate, 'and', 1)
				->orWhere('a.publish_up', '<=', $nowDate, 1)
				->resetDepth();
			$query->where('a.publish_down', 'IS', $nullDate, 'and', 1)
				->orWhere('a.publish_down', '>=', $nowDate, 1)
				->resetDepth();
		}

		// Filter by Date Range or Relative Date
		if (isset($filters['date_filtering']))
		{
			if (!isset($filters['date_field']))
			{
				$filters['date_field'] = 'a.created';
			}
			$dateField = $filters['date_field'];

			switch ($filters['date_filtering'])
			{
				case 'range':
					if (!isset($filters['start_date_range']))
					{
						$filters['start_date_range'] = $nullDate;
					}
					if (!isset($filters['end_date_range']))
					{
						$filters['end_date_range'] = $nullDate;
					}

					$query->where($dateField, '>=', $filters['start_date_range'])
						->where($dateField, '<=', $filters['end_date_range']);
					break;

				case 'relative':
					if (!isset($filters['relative_date']))
					{
						$filters['relative_date'] = 0;
					}
					$relativeDate = (int) $filters['relative_date'];
					$query->where($dateField, '>=', 'DATE_SUB(' . $nowDate . ', INTERVAL ' . $relativeDate . ' DAY)');
					break;

				case 'off':
				default:
					break;
			}
		}

		if (isset($filters['params']))
		{
			$params = $filters['params'];
			$filter = isset($filters['filter']) ? $filters['filter'] : '';

			if (is_object($params) && $params->get('filter_field') != 'hide' && $filter)
			{
				// clean filter variable
				$filter = strtolower($filter);
				$hitsFilter = intval($filter);

				switch ($params->get('filter_field'))
				{
					case 'author':
						$db = \App::get('db');
						$query->whereRaw(
							'LOWER( CASE WHEN a.created_by_alias > '.$db->quote(' ').
							' THEN a.created_by_alias ELSE ua.name END ) LIKE ' . $db->quote('%' . $db->escape($filter, true) . '%', false) . ' '
						);
						break;

					case 'hits':
						$query->where('a.hits', '>=', $hitsFilter);
						break;

					case 'title':
					default: // default to 'title' if parameter is not valid
						$query->whereLike('a.title', $filter);
						break;
				}
			}
		}

		if (!isset($filters['ordering']) || !$filters['ordering'])
		{
			$filters['ordering'] = 'a.ordering';
		}
		if (!isset($filters['direction']) || !in_array(strtolower($filters['direction']), array('asc', 'desc')))
		{
			$filters['direction'] = 'asc';
		}

		$query->order($filters['ordering'], $filters['direction']);

		if (isset($filters['limit']) && $filters['limit'])
		{
			$query->limit((int)$filters['limit']);
		}

		if (!isset($filters['start']))
		{
			$filters['start'] = 0;
		}

		$query->start((int)$filters['start']);

		return $query;
	}

	/**
	 * Build a query based on common filters
	 *
	 * @param   array  $filters
	 * @return  object
	 */
	public static function oneByFilters($filters = array())
	{
		$query = self::all()->setTableAlias('a')->newQuery();

		$query
			->select('a.id')
			->select('a.asset_id')
			->select('a.title')
			->select('a.alias')
			->select('a.title_alias')
			->select('a.introtext')
			->select('a.fulltext')
			->select('CASE WHEN badcats.id IS NULL THEN a.state ELSE 0 END', 'state')
			->select('a.mask')
			->select('a.catid')
			->select('a.created')
			->select('a.created_by')
			->select('a.created_by_alias')
			->select('CASE WHEN a.modified IS NULL THEN a.created ELSE a.modified END', 'modified')
			->select('a.modified_by')
			->select('a.checked_out')
			->select('a.checked_out_time')
			->select('a.publish_up')
			->select('a.publish_down')
			->select('a.images')
			->select('a.urls')
			->select('a.attribs')
			->select('a.version')
			->select('a.parentid')
			->select('a.ordering')
			->select('a.metakey')
			->select('a.metadesc')
			->select('a.access')
			->select('a.hits')
			->select('a.metadata')
			->select('a.featured')
			->select('a.language')
			->select('a.xreference');
			//->from($query->getTableName(), 'a');

		// Join on category table.
		$query->select('c.title', 'category_title')
			->select('c.alias', 'category_alias')
			->select('c.access', 'category_access');
		$query->join('#__categories AS c', 'c.id', 'a.catid', 'left');

		// Join on user table.
		$query->select('u.name', 'author');
		$query->join('#__users AS u', 'u.id', 'a.created_by', 'left');

		// Filter by language
		if (isset($filters['language']) && $filters['language'])
		{
			$query->whereIn('a.language', array(Lang::getTag(), '*'));
		}

		// Join over the categories to get parent category titles
		$query->select('parent.title', 'parent_title')
			->select('parent.id', 'parent_id')
			->select('parent.path', 'parent_route')
			->select('parent.alias', 'parent_alias');
		$query->join('#__categories as parent', 'parent.id', 'c.parent_id', 'left');

		// Join on voting table
		$query->select('ROUND(v.rating_sum / v.rating_count, 0)', 'rating')
			->select('v.rating_count', 'rating_count');
		$query->join('#__content_rating AS v', 'a.id', 'v.content_id', 'left');

		if (isset($filters['id']))
		{
			$query->whereEquals('a.id', (int) $filters['id']);
		}

		if (!User::authorise('core.edit.state', 'com_content')
		 && !User::authorise('core.edit', 'com_content'))
		{
			// Filter by start and end dates.
			$nullDate = null;
			$nowDate = Date::of('now')->toSql();

			$query->where('a.publish_up', 'IS', $nullDate, 'and', 1)
				->orWhere('a.publish_up', '<=', $nowDate, 1)
				->resetDepth();
			$query->where('a.publish_down', 'IS', $nullDate, 'and', 1)
				->orWhere('a.publish_down', '>=', $nowDate, 1)
				->resetDepth();
		}

		// Join to check for category published state in parent categories up the tree
		// If all categories are published, badcats.id will be null, and we just use the article state
		$subquery  = " (SELECT cat.id as id FROM #__categories AS cat JOIN #__categories AS parent ";
		$subquery .= "ON cat.lft BETWEEN parent.lft AND parent.rgt ";
		$subquery .= "WHERE parent.extension = 'com_content'";
		$subquery .= " AND parent.published <= 0 GROUP BY cat.id)";
		$query->join($subquery . ' AS badcats', 'badcats.id', 'c.id', 'left outer');

		// Filter by published state.
		if (isset($filters['state']))
		{
			if (!is_array($filters['state']))
			{
				$filters['state'] = array($filters['state']);
			}
			$query->whereIn('a.state', $filters['state']);
		}

		return $query->row();
	}
}
