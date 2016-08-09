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

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Config\Registry;
use stdClass;
use Request;
use Route;
use Lang;
use Date;
use User;

require_once(__DIR__ . DS . 'attachment.php');
require_once(__DIR__ . DS . 'version.php');
require_once(__DIR__ . DS . 'comment.php');
require_once(__DIR__ . DS . 'author.php');
require_once(__DIR__ . DS . 'tags.php');
require_once(__DIR__ . DS . 'log.php');

/**
 * Wiki model for a page
 */
class Page extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Adapter type
	 *
	 * @var  object
	 */
	protected $adapter = null;

	/**
	 * Component config
	 *
	 * @var  object
	 */
	protected $config = null;

	/**
	 * Page config
	 *
	 * @var  object
	 */
	protected $params = null;

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
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'pagename',
		'namespace'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('pagename', function($data)
		{
			$ns = $this->getNamespace($data['pagename']);

			$error = null;

			if (in_array(strtolower($ns), array('special', 'image', 'file')))
			{
				$error = Lang::txt('COM_WIKI_ERROR_INVALID_TITLE');
			}

			if (strlen($data['pagename']) > 250)
			{
				$error = Lang::txt('Pagename too long');
			}

			return $error ?: false;
		});
	}

	/**
	 * Get the namespace, if one exists
	 * Namespaces are determined by a colon (e.g., Special:Cite)
	 *
	 * @param   string   $pagename  Name to get namespace from
	 * @return  string
	 */
	public function getNamespace($pagename=null)
	{
		if (is_null($pagename))
		{
			$pagename = $this->get('pagename');
		}
		if (strstr($pagename, ':'))
		{
			return strtolower(strstr($pagename, ':', true));
		}
		return '';
	}

	/**
	 * Strip the namespace, if one exists
	 * Namespaces are determined by a colon (e.g., Special:Cite)
	 *
	 * @param   string   $pagename  Name to get namespace from
	 * @return  string
	 */
	public function stripNamespace($pagename=null)
	{
		if (is_null($pagename))
		{
			$pagename = $this->get('pagename');
		}
		if (strstr($pagename, ':'))
		{
			return ltrim(strstr($pagename, ':'), ':');
		}
		return '';
	}

	/**
	 * Generates automatic pagename field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 */
	public function automaticPagename($data)
	{
		if (!isset($data['pagename']) || !$data['pagename'])
		{
			$data['pagename'] = $data['title'];
		}
		return self::normalize($data['pagename']);
	}

	/**
	 * Generates automatic namespace field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 */
	public function automaticNamespace($data)
	{
		if (!isset($data['namespace']))
		{
			$data['namespace'] = $this->getNamespace($data['title']);
		}
		return self::normalize($data['namespace']);
	}

	/**
	 * Get revision
	 *
	 * @return  object
	 */
	public function version()
	{
		return $this->oneToOne('Version', 'id', 'version_id');
	}

	/**
	 * Get parent page
	 *
	 * @return  object
	 */
	public function parent()
	{
		return self::oneOrNew($this->get('parent'));
	}

	/**
	 * Get all aprents
	 *
	 * @return  array
	 */
	public function ancestors()
	{
		$page = $this->parent();

		$ancestors = array();

		if ($page->get('id'))
		{
			$ancestors[] = $page;

			if ($page->get('parent'))
			{
				foreach ($page->ancestors() as $ancestor)
				{
					array_unshift($ancestors, $ancestor);
				}
			}
		}

		return $ancestors;
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Get revisions
	 *
	 * @return  object
	 */
	public function versions()
	{
		return $this->oneToMany('Version', 'page_id');
	}

	/**
	 * Get comments
	 *
	 * @return  object
	 */
	public function comments()
	{
		return $this->oneToMany('Comment', 'page_id');
	}

	/**
	 * Get attachments
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany('Attachment', 'page_id');
	}

	/**
	 * Get suthors
	 *
	 * @return  object
	 */
	public function authors()
	{
		return $this->oneToMany('Author', 'page_id');
	}

	/**
	 * Get links
	 *
	 * @return  object
	 */
	public function links()
	{
		return $this->oneToMany('Link', 'page_id');
	}

	/**
	 * Does the page exist?
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		return ! $this->isNew();
	}

	/**
	 * Is the page locked?
	 *
	 * @return  boolean
	 */
	public function isLocked()
	{
		return ($this->get('protected') == 1);
	}

	/**
	 * Is the page locked?
	 *
	 * @return  boolean
	 */
	public function isDeleted()
	{
		return ($this->get('state') == self::STATE_DELETED);
	}

	/**
	 * Is the page static?
	 *
	 * @return  boolean
	 */
	public function isStatic()
	{
		return ($this->param('mode') == 'static');
	}

	/**
	 * Returns whether a user is an author for a given page
	 *
	 * @param   integer  $user_id
	 * @return  boolean  True if user is an author
	 */
	public function isAuthor($user_id=0)
	{
		if (!$user_id)
		{
			$user_id = User::get('id');
		}

		foreach ($this->authors()->rows() as $author)
		{
			if ($author->get('user_id') == $user_id)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string   $path
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  object
	 */
	public static function oneByPath($path, $scope=null, $scope_id=null)
	{
		$path = explode('/', $path);
		$pagename = array_pop($path);
		$path = implode('/', $path);

		$instance = self::blank()
			->whereEquals('state', self::STATE_PUBLISHED)
			->whereEquals('pagename', $pagename);
		if ($path)
		{
			$instance->whereEquals('path', $path);
		}
		if (!is_null($scope))
		{
			$instance->whereEquals('scope', $scope);
		}
		if (!is_null($scope_id))
		{
			$instance->whereEquals('scope_id', $scope_id);
		}
		return $instance->row();
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string   $title
	 * @param   string   $scope
	 * @param   integer  $scope_id
	 * @return  object
	 */
	public static function oneByTitle($title, $scope=null, $scope_id=null)
	{
		$instance = self::blank()
			->whereEquals('state', self::STATE_PUBLISHED)
			->whereEquals('title', $title);
		if (!is_null($scope))
		{
			$instance->whereEquals('scope', $scope);
		}
		if (!is_null($scope_id))
		{
			$instance->whereEquals('scope_id', $scope_id);
		}
		return $instance->row();
	}

	/**
	 * Strip unwanted characters
	 *
	 * @param   string  $txt  Text to normalize
	 * @return  string
	 */
	public static function normalize($txt)
	{
		return preg_replace("/[^\:a-zA-Z0-9_]/", '', $txt);
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function created($as='')
	{
		return $this->_datetime($as, 'created');
	}

	/**
	 * Return a formatted timestamp for modified date
	 *
	 * @param   string  $as  What data to return
	 * @return  string
	 */
	public function modified($as='')
	{
		if (!$this->get('modified') || $this->get('modified') == '0000-00-00 00:00:00')
		{
			$this->set('modified', $this->get('created'));
		}
		return $this->_datetime($as, 'modified');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as   What data to return
	 * @param   string  $key  Field name
	 * @return  string
	 */
	private function _datetime($as='', $key='created')
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get($key))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get($key))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get($key);
	}

	/**
	 * Generate and return various links to the entry
	 * Link will vary depending upon action desired, such as edit, delete, etc.
	 *
	 * @param   string   $type    The type of link to return
	 * @param   string   $params
	 * @return  boolean
	 */
	public function link($type='', $params=null)
	{
		return $this->adapter()->link($type, $params);
	}

	/**
	 * Get the adapter
	 *
	 * @return  object
	 */
	public function adapter()
	{
		if (!($this->adapter instanceof BaseAdapter))
		{
			$scope = $this->get('scope', 'site');
			$scope = $scope ?: 'site';

			$cls = __NAMESPACE__ . '\\Adapters\\' . ucfirst($scope);

			if (!class_exists($cls))
			{
				$path = __DIR__ . DS . 'adapters' . DS . $scope . '.php';

				if (!is_file($path))
				{
					throw new \InvalidArgumentException(Lang::txt('Invalid adapter type of "%s"', $scope));
				}

				include_once($path);
			}

			$this->adapter = new $cls(
				$this->get('pagename'),
				$this->get('path'),
				$this->get('scope_id')
			);
		}

		return $this->adapter;
	}

	/**
	 * Get tags on the entry
	 * Optinal first agument to determine format of tags
	 *
	 * @param   string   $as     Format to return state in [comma-deliminated string, HTML tag cloud, array]
	 * @param   integer  $admin  Include amdin tags? (defaults to no)
	 * @return  mixed
	 */
	public function tags($as='cloud', $admin=0)
	{
		if (!$this->get('id'))
		{
			switch (strtolower($as))
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

		return $cloud->render($as, array('admin' => $admin));
	}

	/**
	 * Tag the entry
	 *
	 * @param   string   $tags
	 * @param   integer  $user_id
	 * @param   integer  $admin
	 * @return  boolean
	 */
	public function tag($tags=null, $user_id=0, $admin=0)
	{
		$cloud = new Tags($this->get('id'));

		return $cloud->setTags($tags, $user_id, $admin);
	}

	/**
	 * Save the entry
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function save()
	{
		$action = ($this->isNew() ? 'page_created' : 'page_edited');

		// Make sure the path is updated
		if ($this->get('parent'))
		{
			$path = array();

			foreach ($this->ancestors() as $ancestor)
			{
				$path[] = $ancestor->get('pagename');
			}

			$this->set('path', implode('/', $path));
		}

		// Save
		$result = parent::save();

		// Log the action upon success
		if ($result)
		{
			$this->log($action);
		}

		return $result;
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Can't delete what doesn't exist
		if (!$this->get('id'))
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

		// Remove all attachments
		foreach ($this->attachments()->rows() as $attachment)
		{
			if (!$attachment->destroy())
			{
				$this->addError($attachment->getError());
				return false;
			}
		}

		// Remove all links
		foreach ($this->links()->rows() as $link)
		{
			if (!$link->destroy())
			{
				$this->addError($link->getError());
				return false;
			}
		}

		// Remove all aurhors
		foreach ($this->authors()->rows() as $author)
		{
			if (!$author->destroy())
			{
				$this->addError($author->getError());
				return false;
			}
		}

		// Remove all revisions
		foreach ($this->versions()->rows() as $version)
		{
			if (!$version->destroy())
			{
				$this->addError($version->getError());
				return false;
			}
		}

		// Remove all tags
		$this->tag('');

		$this->log('page_deleted');

		// Clear cached data
		\Cache::clean('wiki');

		// Attempt to delete the record
		return parent::destroy();
	}

	/**
	 * Log an action
	 *
	 * @param   string   $action   Action taken
	 * @param   integer  $user_id  Optional ID of user the action was taken on/with
	 * @return  void
	 */
	public function log($action='page_created', $user_id=0)
	{
		$log = Log::blank();
		$log->set('page_id', (int) $this->get('id'));
		$log->set('user_id', ($user_id ? $user_id : User::get('id')));
		$log->set('timestamp', Date::toSql());
		$log->set('action', (string) $action);
		$log->set('actorid', User::get('id'));
		$log->set('comments', json_encode($this->toObject()));

		if (!$log->save())
		{
			$this->setErrors($log->getErrors());
		}
	}

	/**
	 * Get a param value
	 *
	 * @param   string  $key      Property to return
	 * @param   mixed   $default  Value to return if key is not found
	 * @return  mixed
	 */
	public function param($key='', $default=null)
	{
		if (!is_object($this->params))
		{
			$params = new Registry($this->get('params'));

			$this->params = $this->config();
			$this->params->merge($params);
		}

		if ($key)
		{
			return $this->params->get((string) $key, $default);
		}

		return $this->params;
	}

	/**
	 * Get a configuration value
	 *
	 * @param   string  $key      Property to return
	 * @param   mixed   $default  Value to return if key isn't found
	 * @return  mixed
	 */
	public function config($key='', $default=null)
	{
		if (!isset($this->config))
		{
			$this->config = Component::params('com_wiki');
		}

		if ($key)
		{
			return $this->config->get((string) $key, $default);
		}

		return $this->config;
	}

	/**
	 * Get permissions for a user
	 *
	 * @param   string  $action
	 * @param   string  $item
	 * @return  boolean
	 */
	public function access($action='view', $item='page')
	{
		if (!$this->config('access-check-done', false))
		{
			$this->config()->set('access-page-view', true);
			$this->config()->set('access-page-manage', false);
			$this->config()->set('access-page-admin', false);
			$this->config()->set('access-page-create', false);
			$this->config()->set('access-page-delete', false);
			$this->config()->set('access-page-edit', false);
			$this->config()->set('access-page-modify', false);

			$this->config()->set('access-comment-view', false);
			$this->config()->set('access-comment-create', false);
			$this->config()->set('access-comment-delete', false);
			$this->config()->set('access-comment-edit', false);

			// Check if they are logged in
			if (User::isGuest())
			{
				// Not logged-in = can only view
				$this->config()->set('access-check-done', true);
			}

			$option = Request::getCmd('option', 'com_wiki');

			if (!$this->config('access-check-done', false))
			{
				// Is a group set?
				/*if (trim($this->get('group_cn', '')))
				{
					$group = \Hubzero\User\Group::getInstance($this->get('group_cn'));

					// Is this a group manager?
					if ($group && $group->is_member_of('managers', User::get('id')))
					{
						// Allow access to all options
						$this->config()->set('access-page-manage', true);
						$this->config()->set('access-page-create', true);
						$this->config()->set('access-page-delete', true);
						$this->config()->set('access-page-edit', true);
						$this->config()->set('access-page-modify', true);

						$this->config()->set('access-comment-view', true);
						$this->config()->set('access-comment-create', true);
						$this->config()->set('access-comment-delete', true);
						$this->config()->set('access-comment-edit', true);
					}
					else
					{
						// Check permissions based on the page mode (knol/wiki)
						switch ($this->param('mode'))
						{
							// Knowledge article
							// This means there's a defined set of authors
							case 'knol':
								if ($this->get('created_by') == User::get('id')
								 || $this->isAuthor(User::get('id')))
								{
									$this->config()->set('access-page-create', true);
									$this->config()->set('access-page-delete', true);
									$this->config()->set('access-page-edit', true);
									$this->config()->set('access-page-modify', true);
								}
								else if ($this->param('allow_changes'))
								{
									$this->config()->set('access-page-modify', true); // This allows users to suggest changes
								}

								if ($this->param('allow_comments'))
								{
									$this->config()->set('access-comment-view', true);
									$this->config()->set('access-comment-create', true);
								}
							break;

							// Standard wiki
							default:
								// 1 = private to group, 2 = ...um, can't remember
								if ($group && $group->is_member_of('members', User::get('id')))
								{
									$this->config()->set('access-page-create', true);
									if ($this->get('state') != 1)
									{
										$this->config()->set('access-page-delete', true);
										$this->config()->set('access-page-edit', true);
										$this->config()->set('access-page-modify', true);
									}

									$this->config()->set('access-comment-view', true);
									$this->config()->set('access-comment-create', true);
								}
							break;
						}
					}
				}
				// Check if they're a site admin
				else */if (User::authorise('core.manage', $option))
				{
					$this->config()->set('access-page-admin', true);
					$this->config()->set('access-page-manage', true);
					$this->config()->set('access-page-create', true);
					$this->config()->set('access-page-delete', true);
					$this->config()->set('access-page-edit', true);
					$this->config()->set('access-page-modify', true);

					$this->config()->set('access-comment-view', true);
					$this->config()->set('access-comment-create', true);
					$this->config()->set('access-comment-delete', true);
					$this->config()->set('access-comment-edit', true);

					$this->config()->set('access-check-done', true);
				}
				// No group = Site wiki
				else
				{
					$this->config()->set('access-page-create', (User::authorise('core.create', $option) !== false));

					// Check permissions based on the page mode (knol/wiki)
					switch ($this->param('mode'))
					{
						// Knowledge article
						// This means there's a defined set of authors
						case 'knol':
							if ($this->get('created_by') == User::get('id')
							 || $this->isAuthor(User::get('id')))
							{
								$this->config()->set('access-page-delete', true);
								$this->config()->set('access-page-edit', true);
								$this->config()->set('access-page-modify', true);
							}
							else if ($this->param('allow_changes'))
							{
								$this->config()->set('access-page-modify', true); // This allows users to suggest changes
							}

							if ($this->param('allow_comments'))
							{
								$this->config()->set('access-comment-view', true);
								$this->config()->set('access-comment-create', true);
							}
						break;

						// Standard wiki
						default:
							$this->config()->set('access-page-delete', (User::authorise('core.delete', $option) !== false));
							$this->config()->set('access-page-edit', (User::authorise('core.edit', $option) !== false));
							$this->config()->set('access-page-modify', true);

							$this->config()->set('access-comment-view', true);
							$this->config()->set('access-comment-create', true);
						break;
					}
				}
				$this->config()->set('access-check-done', true);
			}
		}

		return $this->config('access-' . (string) $item . '-' . strtolower($action));
	}

	/**
	 * Get the page title
	 * If title isn't set, it will split the camelcase pagename into a spaced title
	 *
	 * @return  string
	 */
	public function transformTitle()
	{
		if (!$this->get('title'))
		{
			$this->set('title', $this->splitPagename($this->get('pagename')));
		}
		return $this->get('title');
	}

	/**
	 * Get the pagename with path
	 *
	 * @return  string
	 */
	public function transformPagename()
	{
		return ($this->get('path') ? $this->get('path') . '/' : '') . $this->get('pagename');
	}

	/**
	 * Splits camel-case page names
	 * e.g., MyPageName => My Page Name
	 *
	 * @param   string  $page  Wiki page name
	 * @return  string
	 */
	public function splitPagename($page)
	{
		if (preg_match("/\s/", $page) || !$page)
		{
			// Already split --- don't split any more.
			return $page;
		}

		// This algorithm is specialized for several languages.
		// (Thanks to Pierrick MEIGNEN)
		// Improvements for other languages welcome.
		static $RE;

		if (!isset($RE))
		{
			$language = strtolower(Lang::getTag());

			// This mess splits between a lower-case letter followed by
			// either an upper-case or a numeral; except that it wont
			// split the prefixes 'Mc', 'De', or 'Di' off of their tails.
			switch ($language)
			{
				case 'fr':
				case 'french':
				case 'fr-fr':
					$RE[] = '/([[:lower:]])((?<!Mc|Di)[[:upper:]]|\d)/';
				break;

				case 'en':
				case 'english':
				case 'en-us':
				case 'en-gb':
				case 'en-au':

				case 'it':
				case 'italian':
				case 'it-IT':

				case 'es':
				case 'spanish':
				case 'es-es':


				case 'de':
				case 'german':
				case 'de-de':
					$RE[] = '/([[:lower:]])((?<!Mc|De|Di)[[:upper:]]|\d)/';
				break;
			}

			$sep = preg_quote('/', '/');

			// This the single-letter words 'I' and 'A' from any following
			// capitalized words.
			switch ($language)
			{
				case 'fr':
				case 'french':
					$RE[] = "/(?<= |${sep}|^)([�])([[:upper:]][[:lower:]])/";
				break;

				case 'en':
				case 'english':
				default:
					$RE[] = "/(?<= |${sep}|^)([AI])([[:upper:]][[:lower:]])/";
				break;
			}

			// Split numerals from following letters.
			$RE[] = '/(\d)([[:alpha:]])/';

			// Split at subpage seperators.
			$RE[] = "/([^${sep}]+)(${sep})/";
			$RE[] = "/(${sep})([^${sep}]+)/";

			foreach ($RE as $key)
			{
				$RE[$key] = $this->pcreFixPosixClasses($key);
			}
		}

		foreach ($RE as $regexp)
		{
			$page = preg_replace($regexp, '\\1 \\2', $page);
		}

		$r = '/(.+?)\:(.+?)/';
		$page = preg_replace($r, '\\1: \\2', $page);
		$page = str_replace('_', ' ', $page);

		return $page;
	}

	/**
	 * This is a helper function which can be used to convert a regexp
	 * which contains POSIX named character classes to one that doesn't.
	 *
	 * Older version (pre 3.x?) of the PCRE library do not support
	 * POSIX named character classes (e.g. [[:alnum:]]).
	 *
	 * All instances of strings like '[:<class>:]' are replaced by the equivalent
	 * enumerated character class.
	 *
	 * Implementation Notes:
	 *
	 * Currently we use hard-coded values which are valid only for
	 * ISO-8859-1.  Also, currently on the classes [:alpha:], [:alnum:],
	 * [:upper:] and [:lower:] are implemented.  (The missing classes:
	 * [:blank:], [:cntrl:], [:digit:], [:graph:], [:print:], [:punct:],
	 * [:space:], and [:xdigit:] could easily be added if needed.)
	 *
	 * This is a hack.  I tried to generate these classes automatically
	 * using ereg(), but discovered that in my PHP, at least, ereg() is
	 * slightly broken w.r.t. POSIX character classes.  (It includes
	 * "\xaa" and "\xba" in [:alpha:].)
	 *
	 * @param   string  $regexp  Regular expression
	 * @return  string
	 */
	private function pcreFixPosixClasses($regexp)
	{
		// First check to see if our PCRE lib supports POSIX character
		// classes.  If it does, there's nothing to do.
		if (preg_match('/[[:upper:]]/', ''))
		{
			return $regexp;
		}

		static $classes = array(
			'alnum' => "0-9A-Za-z\xc0-\xd6\xd8-\xf6\xf8-\xff",
			'alpha' => "A-Za-z\xc0-\xd6\xd8-\xf6\xf8-\xff",
			'upper' => "A-Z\xc0-\xd6\xd8-\xde",
			'lower' => "a-z\xdf-\xf6\xf8-\xff"
		);

		$keys = join('|', array_keys($classes));

		return preg_replace_callback("/\[:($keys):]/", function($matches) use ($classes) { return $classes[$matches[1]]; }, $regexp);
	}

	/**
	 * Calculate the average rating for the page
	 *
	 * @return  integer
	 */
	public function calculateRating()
	{
		$ratings = $this->comments()
			->where('rating', '!=', 0)
			->rows();

		$totalcount = $ratings->count();
		$totalvalue = 0;
		$newrating  = 0;

		if ($totalcount)
		{
			// Add the ratings up
			foreach ($ratings as $item)
			{
				$totalvalue = $totalvalue + $item->get('rating', 0);
			}

			// Find the average of all ratings
			$newrating = $totalvalue / $totalcount;

			// Round to the nearest half
			$newrating = round($newrating*2)/2;
		}

		// Update page with new rating
		$this->set('rating', $newrating);
		$this->set('times_rated', $totalcount);

		return $newrating;
	}
}
