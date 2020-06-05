<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Hubzero\Utility\Str;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for a course
 */
class Courses extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'course';

	/**
	 * Get the item type
	 *
	 * @param   string  $as  Return type as?
	 * @return  string
	 */
	public function type($as=null)
	{
		if ($as == 'title')
		{
			return Lang::txt('Course');
		}
		return parent::type($as);
	}

	/**
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_courses')
		{
			return false;
		}

		if (!Request::getString('gid'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry
	 *
	 * @param   integer  $id  Optional ID to use
	 * @return  boolean
	 */
	public function make($id=null)
	{
		if ($this->exists())
		{
			return true;
		}

		$id = ($id ?: Request::getInt('id', 0));

		include_once \Component::path('com_courses') . DS . 'models' . DS . 'courses.php';
		$course = null;

		if (!$id)
		{
			$course = \Components\Courses\Models\Course::getInstance(Request::getString('gid', ''));

			$id = $course->get('id');
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$course)
		{
			$course = new \Components\Courses\Models\Course($id);
		}

		if (!$course->exists())
		{
			$this->setError(Lang::txt('Course not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $course->get('id'))
		     ->set('created', $course->get('created'))
		     ->set('created_by', $course->get('created_by'))
		     ->set('title', $course->get('title'))
		     ->set('description', Str::truncate($course->get('blurb'), 200))
		     ->set('url', Route::url($course->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
