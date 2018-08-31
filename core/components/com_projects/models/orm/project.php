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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Event;
use User;
use Lang;
use stdClass;

include_once __DIR__ . '/owner.php';
include_once __DIR__ . '/description.php';
include_once __DIR__ . '/connection.php';
include_once __DIR__ . '/activity.php';
include_once __DIR__ . '/type.php';

/**
 * Projects database model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Project extends Relational implements \Hubzero\Search\Searchable
{
	/**
	 * State constants
	 *
	 * @var  integer
	 **/
	const STATE_ARCHIVED = 3;
	const STATE_PENDING  = 5;

	/**
	 * Privacy constants
	 *
	 * @var  integer
	 **/
	const PRIVACY_PRIVATE = 1;
	const PRIVACY_PUBLIC  = 0;
	const PRIVACY_OPEN    = -1;

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'title';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by_user'
	);

	/**
	 * Hubzero\Config\Registry
	 *
	 * @var  object
	 */
	public $config = null;

	/**
	 * Get the URI
	 *
	 * @var  string
	 */
	protected $url = null;

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('alias', function($data)
		{
			$alias = $this->automaticAlias($data);

			// Set name length
			$minLength = (int)$this->config('min_name_length', 3);
			$maxLength = (int)$this->config('max_name_length', 30);

			if (strlen($alias) < $minLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_SHORT');
			}

			if (strlen($alias) > $maxLength)
			{
				return Lang::txt('COM_PROJECTS_ERROR_NAME_TOO_LONG');
			}

			if (preg_match('/[^a-z0-9]/', $alias))
			{
				// Check for illegal characters
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID');
			}

			if (is_numeric($alias))
			{
				// Check for all numeric (not allowed)
				return Lang::txt('COM_PROJECTS_ERROR_NAME_INVALID_NUMERIC');
			}

			return false;
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

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 */
	public function automaticCreatedByUser($data)
	{
		return (isset($data['created_by_user']) && $data['created_by_user'] ? (int)$data['created_by_user'] : (int)User::get('id'));
	}

	/**
	 * Defines a one to many relationship between projects and connections
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function connections()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Connection');
	}

	/**
	 * Defines a one to many relationship between projects and description fields
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function descriptions()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Description', 'project_id');
	}

	/**
	 * Defines a one to many relationship between projects and team
	 *
	 * @return  \Hubzero\Database\Relationship\OneToMany
	 **/
	public function team()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Owner', 'projectid');
	}

	/**
	 * Defines a one to one relationship between project and type
	 *
	 * @return  \Hubzero\Database\Relationship\OneToOne
	 **/
	public function type()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Type', 'id', 'type');
	}

	/**
	 * Check if the project is private
	 *
	 * @return  boolean
	 */
	public function isPrivate()
	{
		return ($this->get('private') == self::PRIVACY_PRIVATE);
	}

	/**
	 * Check if the project is public
	 *
	 * @return  boolean
	 */
	public function isPublic()
	{
		if ($this->isNew())
		{
			return false;
		}

		if ($this->get('private') == self::PRIVACY_PRIVATE)
		{
			return false;
		}

		return true;
	}

	/**
	 * Check if the project is open
	 *
	 * @return  boolean
	 */
	public function isOpen()
	{
		if ($this->isNew())
		{
			return false;
		}

		if ($this->get('private') != self::PRIVACY_OPEN)
		{
			return false;
		}

		return true;
	}

	/**
	 * Is project archived?
	 *
	 * @return  boolean
	 */
	public function isArchived()
	{
		return ($this->get('state') == self::STATE_ARCHIVED);
	}

	/**
	 * Is project deleted?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Is project pending approval?
	 *
	 * @return  boolean
	 */
	public function isPending()
	{
		return ($this->get('state') == self::STATE_PENDING);
	}

	/**
	 * Is project suspended?
	 *
	 * @return     boolean
	 */
	public function isInactive()
	{
		return ($this->get('state') == 0 && !$this->inSetup());
	}

	/**
	 * Is project provisioned?
	 *
	 * @return  boolean
	 */
	public function isProvisioned()
	{
		return ($this->get('provisioned') == 1);
	}

	/**
	 * Is project in setup?
	 *
	 * @return  boolean
	 */
	public function inSetup()
	{
		$setupComplete = $this->config()->get('confirm_step') ? 3 : 2;

		return ($this->get('setup_stage') < $setupComplete);
	}

	/**
	 * Get a configuration value
	 * If no key is passed, it returns the configuration object
	 *
	 * @param   string  $key      Config property to retrieve
	 * @param   mixed   $default  Default value if property is not found
	 * @return  mixed
	 */
	public function config($key=null, $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = \Component::params('com_projects');
		}
		if ($key)
		{
			return $this->config->get($key, $default);
		}
		return $this->config;
	}

	/**
	 * Deletes the existing/current model
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		$data = $this->toArray();

		// Trigger before delete event
		Event::trigger('projects.onProjectBeforeDelete', array($data));

		// Remove associated data
		foreach ($this->connections as $connection)
		{
			if (!$connection->destroy())
			{
				$this->addError($connection->getError());
				return false;
			}
		}

		foreach ($this->descriptions as $description)
		{
			if (!$description->destroy())
			{
				$this->addError($description->getError());
				return false;
			}
		}

		foreach ($this->team as $team)
		{
			if (!$team->destroy())
			{
				$this->addError($team->getError());
				return false;
			}
		}

		// Attempt to delete the record
		$result = parent::destroy();

		if ($result)
		{
			// Trigger after delete event
			Event::trigger('projects.onProjectAfterDelete', array($data));
		}

		return $result;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string   $type  The type of link to return
	 * @return  boolean
	 */
	public function link($type = '')
	{
		if (!isset($this->url))
		{
			$this->url = 'index.php?option=com_projects&alias=' . $this->get('alias');
		}

		$type = strtolower($type);

		// If it doesn't exist or isn't published
		switch ($type)
		{
			case 'setup':
			case 'edit':
				$link = $this->url . '&task=' . $type;
			break;

			case 'thumb':
				$link = $this->picture();
			break;

			case 'stamp':
				$link = 'index.php?option=com_projects&task=get';
			break;

			case 'permalink':
			default:
				$link = $this->url;

				if ($type)
				{
					if (\Plugin::isEnabled('projects', $type))
					{
						$link .= '&active=' . $type;
					}
				}
			break;
		}

		return $link;
	}

	/**
	 * Generate and return path to a picture for the project
	 *
	 * @param   string   $size      Thumbnail (thumb) or full size (master)?
	 * @param   boolean  $realpath  Return the actual file path? When FALSE, it returns a link to /files/{hash}
	 * @return  string
	 */
	public function picture($size = 'thumb', $realpath = false)
	{
		$src  = '';
		$path = PATH_APP . DS . trim($this->config()->get('imagepath', '/site/projects'), DS) . DS . $this->get('alias') . DS . 'images';

		if ($size == 'thumb')
		{
			// Does a thumb exist?
			if (file_exists($path . DS . 'thumb.png'))
			{
				$src = $path . DS . 'thumb.png';
			}

			// No thumb. Try to create it...
			if (!$src && $this->get('picture'))
			{
				$thumb = \Components\Projects\Helpers\Html::createThumbName($this->get('picture'));

				if ($thumb && file_exists($path . DS . $thumb))
				{
					$src = $path . DS . $thumb;
				}
			}
		}
		elseif (is_file($path . DS . 'master.png') && $size != 'original')
		{
			$src = $path . DS . 'master.png';
		}
		else
		{
			// Get the picture if set
			if ($this->get('picture') && is_file($path . DS . $this->get('picture')))
			{
				$src = $path . DS . $this->get('picture');
			}
		}

		// Still no file? Let's use the default
		if (!$src)
		{
			$deprecated = array(
				'components/com_projects/site/assets/img/project.png',
				'components/com_projects/assets/img/project.png',
				'components/com_projects/site/assets/img/projects-large.gif',
				'components/com_projects/assets/img/projects-large.gif'
			);

			$path = trim($this->config()->get('defaultpic', 'components/com_projects/site/assets/img/project.png'), DS);

			if (in_array($path, $deprecated))
			{
				$path = 'components/com_projects/site/assets/img/project.svg';
				$rootPath = PATH_CORE;
			}
			else
			{
				$rootPath = PATH_APP;
			}

			$src = $rootPath . DS . $path;
		}

		// Gnerate a file link
		if (!$realpath)
		{
			$src = with(new \Hubzero\Content\Moderator($src, 'public'))->getUrl();
		}

		return $src;
	}

	/**
	 * Get total number of records that will be indexed for search
	 *
	 * @return  integer
	 */
	public static function searchTotal()
	{
		$total = self::all()->total();
		return $total;
	}

	/**
	 * Get records to be included in search index
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
	 * Namespace used for Search
	 *
	 * @return  string
	 */
	public static function searchNamespace()
	{
		return 'project';
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
	 * @return  object
	 */
	public function searchResult()
	{
		if ($this->get('state') != self::STATE_PUBLISHED)
		{
			return false;
		}
		$page = new stdClass;

		if ($this->get('state') == self::STATE_PUBLISHED && ($this->isPublic() || $this->isOpen()))
		{
			$access_level = 'public';
		}
		else
		{
			$access_level = 'private';
		}

		$page->url = rtrim(Request::root(), '/') . Route::urlForClient('site', $this->link());
		$page->access_level = $access_level;
		$page->owner_type = 'user';
		$team = array();
		$team = array_map(
			function($member)
			{
				if ($member['status'] == 1 && !empty($member['userid']) && $member['userid'] > 0)
				{
					return $member['userid'];
				}
			},
			$this->team->toArray()
		);
		$page->owner = $team;
		$page->id = $this->searchId();
		$page->title = $this->title;
		$page->hubtype = self::searchNamespace();
		$page->description = \Hubzero\Utility\Sanitize::stripAll($this->about);

		return $page;
	}
}
