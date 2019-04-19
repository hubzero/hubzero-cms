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

namespace Components\Courses\Models;

use Components\Courses\Tables;
use Components\Courses\Models\Assets;
use Component;
use Request;
use Date;
use User;
use Lang;
use App;

require_once Component::path('com_courses') . '/tables/asset.association.php';
require_once Component::path('com_courses') . '/tables/asset.php';
require_once Component::path('com_courses') . '/models/assets/handler.php';
require_once Component::path('com_courses') . '/models/assets/content.php';
require_once Component::path('com_courses') . '/models/base.php';
require_once Component::path('com_courses') . '/models/section/date.php';

/**
 * Asset model class for a course
 */
class Asset extends Base
{
	/**
	 * Table class name
	 *
	 * @var string
	 */
	protected $_tbl_name = '\\Components\\Courses\\Tables\\Asset';

	/**
	 * Object scope
	 *
	 * @var string
	 */
	protected $_scope = 'asset';

	/**
	 * Registry
	 *
	 * @var object
	 */
	protected $_params = null;

	/**
	 * Auxiliary keys for assets (links to other tables)
	 *
	 * @var array
	 */
	protected static $_aux_tablekeys = array(
		'progress_factors' => array(
			'id',
			'section_id'
		)
	);

	/**
	 * Constructor
	 *
	 * @param   mixed $oid Integer, array, or object
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		parent::__construct($oid);

		$this->_params = Component::params('com_courses');
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param	string $property The name of the property
	 * @param	mixed  $default  The default value if property not found
	 * @return	mixed  The value of the property
	 */
	public function get($property, $default=null)
	{
		if (isset($this->_tbl->$property))
		{
			return $this->_tbl->$property;
		}
		else if (isset($this->_tbl->{'__' . $property}))
		{
			return $this->_tbl->{'__' . $property};
		}
		else if (in_array($property, self::$_section_keys))
		{
			$tbl = new Tables\SectionDate($this->_db);
			$tbl->load($this->get('id'), 'asset', $this->get('section_id'));

			$this->set('publish_up', $tbl->get('publish_up', ''));
			$this->set('publish_down', $tbl->get('publish_down', ''));

			return $tbl->get($property, $default);
		}
		else if (strpos($property, '.') !== false)
		{
			$parts = explode('.', $property);
			if (isset($parts[0]) && array_key_exists($parts[0], self::$_aux_tablekeys))
			{
				$key = str_replace('_', ' ', $parts[0]);
				$key = ucwords($key);
				$key = str_replace(' ', '', $key);
				$tbl = "Components\\Courses\\Tables\\{$key}";
				$tbl = new $tbl($this->_db);
				$aux = array();
				foreach (self::$_aux_tablekeys[$parts[0]] as $item)
				{
					$k = $item;
					if ($item == 'id')
					{
						$k = $this->_scope . '_' . $item;
					}
					$aux[$k] = $this->get((string)$item);
				}
				$tbl->load($aux);

				return $tbl->get($parts[1], $default);
			}
		}

		return $default;
	}

	/**
	 * Get the asset path
	 *
	 * @param   integer $course  Course ID
	 * @param   boolean $withUrl Append the URL?
	 * @return  string
	 */
	public function path($course=0, $withUrl=true)
	{
		if (!$this->get('id'))
		{
			return false;
		}

		// /site/courses/{course ID}/{asset ID}/{asset file}
		$assetPath = $this->get('path');

		if (DS != '/')
		{
			$assetPath = str_replace('/', DS, $assetPath);
		}

		$path = DS . trim($this->_params->get('uploadpath', '/site/courses'), DS) . DS . $assetPath;
		if ($withUrl)
		{
			$path .= DS . ltrim($this->get('url'), DS);
		}

		// Override path for exam type assets
		// Override path for url/link type assets
		if (in_array(strtolower($this->get('type')), array('form', 'link', 'url')))
		{
			$path = $this->get('url');
		}

		return $path;
	}

	/**
	 * Store changes to this entry
	 *
	 * @param   boolean $check Perform data validation check?
	 * @return  boolean False if error, True on success
	 */
	public function store($check=true)
	{
		$value = parent::store($check);

		if ($value && $this->get('section_id'))
		{
			$dt = new Tables\SectionDate($this->_db);
			$dt->load(
				$this->get('id'),
				$this->_scope,
				$this->get('section_id')
			);
			$dt->set('publish_up', $this->get('publish_up'));
			$dt->set('publish_down', $this->get('publish_down'));
			if (!$dt->store())
			{
				$this->setError($dt->getError());
			}
		}

		$properties = get_object_vars($this->_tbl);
		foreach ($properties as $k => $v)
		{
			$kname = substr($k, 2);
			if (!empty($kname) && array_key_exists($kname, self::$_aux_tablekeys))
			{
				$key = $kname;
				$key = str_replace('_', ' ', $key);
				$key = ucwords($key);
				$key = str_replace(' ', '', $key);
				$tbl = "Components\\Courses\\Tables\\{$key}";
				$tbl = new $tbl($this->_db);

				if ($v == 'delete')
				{
					$aux = array();
					foreach (self::$_aux_tablekeys[$kname] as $item)
					{
						$k = $item;
						if ($item == 'id')
						{
							$k = $this->_scope . '_' . $item;
						}
						$aux[$k] = $this->get((string)$item);
					}

					$tbl->load($aux);
					$tbl->delete();
				}
				else
				{
					$tbl->save($v);
				}
			}
		}

		return $value;
	}

	/**
	 * Delete an asset
	 *   Deleted asset_associations until there is only one
	 *   association left, then it deletes the association,
	 *   the asset record, and asset file(s)
	 *
	 * @return  boolean True on success, false on error
	 */
	public function delete()
	{
		// Remove dates
		if ($this->get('section_id'))
		{
			$dt = new Tables\SectionDate($this->_db);
			$dt->load($this->get('id'), $this->_scope, $this->get('section_id'));
			if (!$dt->delete())
			{
				$this->setError($dt->getError());
			}
		}

		// Remove this record from the database and log the event
		return parent::delete();
	}

	/**
	 * Check a user's authorization
	 *
	 * @param   string  $action Action to check
	 * @param   string  $item   Item type to check action against
	 * @return  boolean True if authorized, false if not
	 */
	public function access($action='view', $item='section')
	{
		return $this->config()->get('access-' . strtolower($action) . '-' . $item);
	}

	/**
	 * Track asset views
	 *
	 * @param   object $course \Components\Courses\Models\Course
	 * @return  mixed
	 */
	public function logView($course=null)
	{
		require_once dirname(__DIR__) . DS . 'tables' . DS . 'asset.views.php';

		if (!$course || !is_object($course))
		{
			$gid      = Request::getString('gid');
			$offering = Request::getString('offering');
			$section  = Request::getString('section');

			$course = new Course($gid);
			$course->offering($offering);
			$course->offering()->section($section);
		}

		$member = $course->offering()->section()->member(User::get('id'));

		if (!$member->get('id'))
		{
			$member = $course->offering()->member(User::get('id'));
		}

		if (!$member || !is_object($member) || !$member->get('id'))
		{
			return false;
		}

		$view = new Tables\AssetViews($this->_db);
		$view->asset_id          = $this->_tbl->id;
		$view->course_id         = $this->get('course_id');
		$view->viewed            = Date::toSql();
		$view->viewed_by         = $member->get('id');
		$view->ip                = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
		$view->url               = (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
		$view->referrer          = (isset($_SERVER['HTTP_REFERRER']) ? $_SERVER['HTTP_REFERRER'] : '');
		$view->user_agent_string = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
		$view->session_id        = App::get('session')->getId();
		if (!$view->store())
		{
			$this->setError($view->getError());
		}
	}

	/**
	 * Render an asset
	 *
	 * @param   object $course \Components\Courses\Models\Course
	 * @param   string $option Component name
	 * @return  string
	 */
	public function render($course=null, $option='com_courses')
	{
		$type    = strtolower($this->get('type'));
		$subtype = strtolower($this->get('subtype'));
		$layout  = 'default';

		$this->logView($course);

		// Check to see that the view template exists, otherwise, use the default
		if (file_exists(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'site' . DS . 'views' . DS . 'assets' . DS . 'tmpl' . DS . $type . '_' . $subtype . '.php'))
		{
			$layout = $type . '_' . $subtype;
		}
		elseif (file_exists(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'site' . DS . 'views' . DS . 'assets' . DS . 'tmpl' . DS . $type . '.php'))
		{
			$layout = $type;
		}

		$view = new \Hubzero\Component\View(array(
			'base_path' => PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'site',
			'name'      => 'assets',
			'layout'    => $layout
		));
		$view->asset   = $this->_tbl;
		$view->model   = $this;
		$view->course  = $course;
		$view->option  = $option;

		return $view->loadTemplate();
	}

	/**
	 * Download a wiki file
	 *
	 * @param   object $course \Components\Courses\Models\Course
	 * @return  void
	 */
	public function download($course)
	{
		// Get some needed libraries
		if (!$course->access('view'))
		{
			App::abort(404, Lang::txt('COM_COURSES_NO_COURSE_FOUND'));
			return;
		}

		// Get the scope of the parent page the file is attached to
		$filename = Request::getString('file', '');
		if (substr(strtolower($filename), 0, strlen('image:')) == 'image:')
		{
			$filename = substr($filename, strlen('image:'));
		}
		else if (substr(strtolower($filename), 0, strlen('file:')) == 'file:')
		{
			$filename = substr($filename, strlen('file:'));
		}
		$filename = urldecode($filename);

		// Get the configured upload path
		$config = Component::params('com_courses');
		$base_path = $this->path($course->get('id'));

		// Does the path start with a slash?
		$filename = DS . ltrim($filename, DS);

		// Does the beginning of the $attachment->path match the config path?
		if (substr($filename, 0, strlen($base_path)) == $base_path)
		{
			// Yes - this means the full path got saved at some point
		}
		else
		{
			// No - append it
			$filename = $base_path . $filename;
		}

		// Add PATH_CORE
		$filename = PATH_APP . $filename;

		// Ensure the file exist
		if (!file_exists($filename))
		{
			App::abort(404, Lang::txt('COM_COURSES_FILE_NOT_FOUND').' '.$filename);
			return;
		}

		// Initiate a new content server and serve up the file
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename($filename);
		$xserver->disposition('inline');
		$xserver->acceptranges(false); // @TODO fix byte range support

		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('COM_COURSES_SERVER_ERROR'));
		}
		else
		{
			exit;
		}
		return;
	}

	/**
	 * Get a count or list of parents for this entry
	 *
	 * @param   array $filters Filters to apply to results query
	 * @return  array
	 */
	public function parents($filters=array())
	{
		if (!isset($filters['asset_id']))
		{
			$filters['asset_id'] = (int) $this->get('id');
		}

		$tbl = new Tables\AssetAssociation($this->_db);

		if (isset($filters['count']) && $filters['count'])
		{
			return $tbl->count($filters);
		}

		if (!($results = $tbl->find($filters)))
		{
			$results = array();
		}

		return $results;
	}

	/**
	 * Get the unit(s) to which this asset is attached
	 *
	 * @return  array
	 */
	public function units()
	{
		if (isset($this->units))
		{
			return $this->units;
		}
		else
		{
			$this->units = array();
		}

		$assets = $this->_tbl->find(array('w'=>array('asset_id'=>$this->get('id'))));
		if ($assets && count($assets) > 0)
		{
			foreach ($assets as $asset)
			{
				if (isset($asset->unit_id))
				{
					if (!isset($this->units[$asset->unit_id]))
					{
						$this->units[$asset->unit_id] = new Unit($asset->unit_id);
					}
				}
			}
		}

		return $this->units;
	}

	/**
	 * Copy an entry and associated data
	 *
	 * @param  bool $forms whether or not to duplicate forms as well
	 * @return bool
	 */
	public function copy($forms=true)
	{
		// Keep track of the original id
		$originalId = $this->get('id');

		// Reset the ID. This will force store() to create a new record.
		$this->set('id', 0);

		if (!$this->store())
		{
			return false;
		}

		// If this is a form...
		if ($forms && $this->get('type') == 'form')
		{
			require_once __DIR__ . DS . 'form.php';
			require_once __DIR__ . DS . 'formDeployment.php';
			require_once __DIR__ . DS . 'formRespondent.php';

			// Copy the form as well...look up by asset_id
			if ($form = PdfForm::loadByAssetId($originalId))
			{
				// This will either return the form id or the deployment crumb
				$identifier = $form->copy();
				$form->setAssetId($this->get('id'));

				$this->set('url', $identifier);
				$this->store();
			}
		}

		return true;
	}

	/**
	 * Specific child asset handler object of type
	 *
	 * @return object child asset handler
	 */
	public function loadHandler()
	{
		$handlerName = $this->get('type');
		$filePath = Component::path('com_courses') . '/models/assets/' . $handlerName . '.php';
		if (file_exists($filePath))
		{
			require_once $filePath;
			$handlerClassString = 'Components\\Courses\\Models\\Assets\\' . ucfirst($handlerName);
			$handlerModel = new $handlerClassString($this->_db);
			return $handlerModel;
		}
		return false;
	}
}
