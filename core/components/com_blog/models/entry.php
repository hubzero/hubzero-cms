<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Blog\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Form\Form;
use Filesystem;
use Component;
use Lang;
use User;
use Date;
use stdClass;

require_once __DIR__ . DS . 'tags.php';
require_once __DIR__ . DS . 'comment.php';

/**
 * Model class for a blog entry
 */
class Entry extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'blog';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'publish_up';

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
		'title'    => 'notempty',
		'content'  => 'notempty',
		'scope'    => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias',
		'publish_up',
		'publish_down'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by',
		'params'
	);

	/**
	 * Fields to be parsed
	 *
	 * @var array
	 */
	protected $parsed = array(
		'content'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Scope adapter
	 *
	 * @var  object
	 */
	protected $adapter = null;

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
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 100)
		{
			$alias = substr($alias . ' ', 0, 100);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAllowComments($data)
	{
		$allow = intval(isset($data['allow_comments']) ? $data['allow_comments'] : 1);

		return ($allow ? $allow : 0);
	}

	/**
	 * Generates automatic publish_up field value
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
			$publish_up = ($data['id'] ? $this->created : \Date::toSql());
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
	 * Generates automatic params field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParams($data)
	{
		if (!isset($data['params']) || is_null($data['params']))
		{
			$data['params'] = '';
		}
		return $data['params'];
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string   $alias     The alias to load by
	 * @param   string   $scope     Scope
	 * @param   integer  $scope_id  Scope ID
	 * @return  mixed
	 */
	public static function oneByScope($alias, $scope, $scope_id)
	{
		return self::blank()
			->whereEquals('alias', $alias)
			->whereEquals('scope', $scope)
			->whereEquals('scope_id', $scope_id)
			->row();
	}

	/**
	 * Has the publish window started?
	 *
	 * @return  boolean
	 */
	public function started()
	{
		// If it doesn't exist or isn't published
		if ($this->isNew())
		{
			return false;
		}

		if ($this->get('publish_up')
		 && $this->get('publish_up') != '0000-00-00 00:00:00'
		 && $this->get('publish_up') > Date::toSql())
		{
			return false;
		}

		return true;
	}

	/**
	 * Has the publish window ended?
	 *
	 * @return  boolean
	 */
	public function ended()
	{
		// If it doesn't exist or isn't published
		if ($this->isNew())
		{
			return true;
		}

		if ($this->get('publish_down')
		 && $this->get('publish_down') != '0000-00-00 00:00:00'
		 && $this->get('publish_down') <= Date::toSql())
		{
			return true;
		}

		return false;
	}

	/**
	 * Has the offering started?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == -1 || $this->get('state') == 2);
	}

	/**
	 * Check if the entry is available
	 *
	 * @return  boolean
	 */
	public function isAvailable()
	{
		// If it doesn't exist or isn't published
		if ($this->isNew() || $this->isDeleted())
		{
			return false;
		}

		// Make sure the item is published and within the available time range
		if ($this->started() && !$this->ended())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		if (file_exists(Component::path('com_members') . DS . 'models' . DS . 'member.php'))
		{
			include_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

			return $this->belongsToOne('Components\Members\Models\Member', 'created_by');
		}
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get a list of comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneToMany('Comment', 'entry_id');
	}

	/**
	 * Get tags on an entry
	 *
	 * @param   string   $what   Data format to return (string, array, cloud)
	 * @param   integer  $admin  Get admin tags? 0=no, 1=yes
	 * @return  mixed
	 */
	public function tags($what='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($what))
			{
				case 'array':
					return array();
				break;

				case 'string':
				case 'cloud':
				case 'html':
				default:
					return '';
				break;
			}
		}

		$cloud = new Tags($this->get('id'));
		if ($what == 'object')
		{
			return $cloud;
		}

		return $cloud->render($what, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags     Tags to apply
	 * @param   integer  $user_id  ID of tagger
	 * @param   integer  $admin    Tag as admin? 0=no, 1=yes
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Get the state of the entry as either text or numerical value
	 *
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
	 */
	public function state($as='text')
	{
		$as = strtolower($as);

		if ($as == 'text')
		{
			switch ($this->get('state'))
			{
				case 1:
					return 'published';
					break;
				case 2:
					return 'trashed';
					break;
				case 0:
				default:
					return 'unpublished';
					break;
			}
		}

		return $this->get('state');
	}

	/**
	 * Get the access level of the entry as either text or numerical value
	 *
	 * @param   string  $as  Format to return state in [text, number]
	 * @return  mixed   String or Integer
	 */
	public function visibility($as='text')
	{
		static $access;

		if ($as == 'text')
		{
			if (!isset($access))
			{
				$access = \Html::access('assetgroups');
			}
			foreach ($access as $a)
			{
				if ($this->get('access') == $a->value)
				{
					return $a->text;
				}
			}
		}

		return $this->get('access');
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string  $type    The type of link to return
	 * @param   mixed   $params  Optional string or associative array of params to append
	 * @return  string
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->link($type, $params);
	}

	/**
	 * Retrieve a property from the internal item object
	 *
	 * @param   string  $key  Property to retrieve
	 * @return  string
	 */
	public function item($key='')
	{
		return $this->adapter()->item($key);
	}

	/**
	 * Get the path to the storage location for
	 * this blog's files
	 *
	 * @return  string
	 */
	public function filespace()
	{
		return $this->adapter()->filespace();
	}

	/**
	 * Return the adapter for this entry's scope,
	 * instantiating it if it doesn't already exist
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!$this->adapter)
		{
			$scope = strtolower($this->get('scope'));

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . '/adapters/' . $scope . '.php';

				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid scope of "%s" for entry #%s', $scope, $this->get('id')));
				}

				include_once $path;
			}

			$this->adapter = with(new $cls($this->get('scope_id')))
				->set('publish_up', $this->get('publish_up'))
				->set('id', $this->get('id'))
				->set('alias', $this->get('alias'));
		}

		return $this->adapter;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function published($as='')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('publish_up'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('publish_up'))->toLocal($as);
		}

		return $this->get('publish_up');
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_blog');
			$p->merge($params);

			$this->params = $p;
		}

		return $this->params;
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$field = 'content';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$published = Date::of($this->get('publish_up'));

			$scope  = $published->toLocal('Y') . '/';
			$scope .= $published->toLocal('m');

			$params = array(
				'filepath' => $this->adapter()->filespace(),
				'option'   => $this->adapter()->get('option'),
				'scope'    => $this->adapter()->get('scope') . '/' . $scope,
				'pagename' => $this->get('alias'),
				'pageid'   => 0,
				'filepath' => $this->adapter()->get('path'),
				'domain'   => ''
			);

			$this->$property = \Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string   $action  Action to check
	 * @return  boolean  True if authorized, false if not
	 */
	public function access($action='view')
	{
		if (!is_object($this->params))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_blog');
			$p->merge($params);

			$this->params = $p;
		}

		if (!$this->params->get('access-check-done', false))
		{
			// Set NOT viewable by default
			// We need to ensure the forum is published first
			$this->params->set('access-view-entry', false);

			if (!$this->isNew() && $this->isAvailable())
			{
				$this->params->set('access-view-entry', true);
			}

			if (User::isGuest())
			{
				// Do not allow logged-out users to see private, 
				// or 'registered' entries.
				if ($this->get('state') != 1)
				{
					$this->params->set('access-view-entry', false);
				}
				$this->params->set('access-check-done', true);
			}
			else
			{
				// Check if they're a site admin
				foreach (array('admin', 'manage', 'delete', 'edit', 'edit-state', 'edit-own') as $option)
				{
					$this->params->set(
						'access-' . $option . '-entry',
						User::authorise('core.' . ($option == 'admin' ? 'admin' : 'manage'), $this->get('id'))
					);
				}

				// If they're not an admin
				if (!$this->params->get('access-admin-entry')
				 && !$this->params->get('access-manage-entry'))
				{
					// Disallow access if the entry is private
					if ($this->get('state') == 0)
					{
						$this->params->set('access-view-entry', false);
					}

					// Was the entry created by the current user?
					if ($this->get('created_by') == User::get('id'))
					{
						// Give full access
						$this->params->set('access-view-entry', true);
						$this->params->set('access-manage-entry', true);
						$this->params->set('access-delete-entry', true);
						$this->params->set('access-edit-entry', true);
						$this->params->set('access-edit-state-entry', true);
						$this->params->set('access-edit-own-entry', true);
					}
				}
				else
				{
					$this->params->set('access-view-entry', true);
				}

				$this->params->set('access-check-done', true);
			}
		}
		return $this->params->get('access-' . strtolower($action) . '-entry');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if ($this->isNew())
		{
			return true;
		}

		// Remove comments
		foreach ($this->comments()->rows() as $comment)
		{
			if (!$comment->destroy())
			{
				$this->addError($comment->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Validates the set data attributes against the model rules
	 *
	 * @return  bool
	 **/
	public function validate()
	{
		$valid = parent::validate();

		if ($valid)
		{
			$results = \Event::trigger('content.onContentBeforeSave', array(
				'com_blog.entry.content',
				&$this,
				$this->isNew()
			));

			foreach ($results as $result)
			{
				if ($result === false)
				{
					$this->addError(Lang::txt('Content failed validation.'));
					$valid = false;
				}
			}
		}

		return $valid;
	}

	/**
	 * Transforms a namespace to an object
	 *
	 * @return  object  An an object holding the namespace data
	 */
	public function toObject()
	{
		$data = parent::toObject();

		$this->access();
		$data->params = $this->params->toObject();

		return $data;
	}

	/**
	 * Get a form
	 *
	 * @return  object
	 */
	public function getForm()
	{
		$file = __DIR__ . '/forms/' . strtolower($this->getModelName()) . '.xml';
		$file = Filesystem::cleanPath($file);

		$form = new Form('blog', array('control' => 'fields'));

		if (!$form->loadFile($file, false, '//form'))
		{
			$this->addError(Lang::txt('JERROR_LOADFILE_FAILED'));
		}

		$data = $this->toArray();
		$data['params'] = $this->params->toArray();

		$form->bind($data);

		return $form;
	}

	/**
	 * Namespace used for solr Search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		$searchNamespace = 'blog';
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
		if ($this->state == 2)
		{
			return false;
		}
		$blog = new stdClass;
		$blog->title = $this->title;
		$blog->hubtype = self::searchNamespace();
		$blog->id = $this->searchId();
		$blog->description = $this->content;
		$creator = $this->creator;
		$blog->author[] = $creator->name;
		$tags = $this->tags('object')->tags();
		if (!empty($tags))
		{
			foreach ($tags as $tag)
			{
				$title = $tag->get('raw_tag', '');
				$description = $tag->get('tag', '');
				$label = $tag->get('label', '');
				$blog->tags[] = array(
					'id' => 'tag-' . $tag->id,
					'title' => $title,
					'description' => $description,
					'access_level' => $tag->admin == 0 ? 'public' : 'private',
					'type' => 'blog-tag',
					'badge_b' => $label == 'badge' ? true : false
				);
			}
		}
		$blog->owner_type = 'user';
		$blog->owner = $creator->id;
		if (($this->state == 1) && ($this->access < 2) && ($this->scope == 'site'))
		{
			$blog->access_level = $this->access == 1 ? 'public' : 'registered';
		}
		elseif ($this->scope != 'site')
		{
			if ($this->scope == 'member')
			{
				if ($this->state == 0)
				{
					$blog->access_level = 'private';
				}
				else
				{
					if ($creator->get('blocked') == 0 && $creator->get('approved') > 0)
					{
						$access = ($creator->get('access') >= $this->access) ? $creator->get('access') : $this->access;
						if ($access > 2)
						{
							$blog->access_level = 'private';
						}
						elseif ($access == 2)
						{
							$blog->access_level = 'registered';
						}
						elseif ($access == 1)
						{
							$blog->access_level = 'private';
						}
					}
				}
				$blog->owner_type = 'user';
				$blog->owner = $this->scope_id;
			}
			elseif ($this->scope == 'group')
			{
				if ($this->state == 0)
				{
					$blog->access_level = 'private';
				}
				else
				{
					$group = \Hubzero\User\Group::getInstance($this->scope_id);
					if ($group)
					{
						$groupName = $group->get('cn');
						$blogAccess = \Hubzero\User\Group\Helper::getPluginAccess($group, 'blog');
						if ($blogAccess == 'anyone')
						{
							$groupAccess = 1;
						}
						elseif ($blogAccess == 'registered')
						{
							$groupAccess = 2;
						}
						else
						{
							$groupAccess = 4;
						}
						$access = ($groupAccess >= $this->access) ? $groupAccess : $this->access;
						if ($access > 2)
						{
							$blog->access_level = 'private';
						}
						elseif ($access == 2)
						{
							$blog->access_level = 'registered';
						}
						elseif ($access == 1)
						{
							$blog->access_level = 'public';
						}
					}
					else
					{
						$blog->access_level = 'private';
					}
				}
				$blog->owner_type = 'group';
				$blog->owner = $this->scope_id;
			}
		}
		$blog->url = rtrim(Request::root(), '/') . Route::urlForClient('site', $this->link());
		return $blog;
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
}
