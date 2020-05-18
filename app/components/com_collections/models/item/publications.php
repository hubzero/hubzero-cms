<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Publications\Models\Publication;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for an item
 */
class Publications extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'publication';

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
			return Lang::txt('Resource');
		}
		return parent::type($as);
	}

	/**
	 * Check if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_publications')
		{
			return false;
		}

		if (!Request::getInt('id', 0))
		{
			if (!Request::getString('v', ''))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Create an item entry for a publication
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
		$v = Request::getInt('v', 0);

		include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';
		$resource = null;

		if ($id && $v) {
			$resource = new \Components\Publications\Models\Publication($id, $v, null);
			$uid = $resource->version->id;
		} else {
			return false;
		}

		$this->_tbl->loadType($uid, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$uid)
		{
			$this->setError(Lang::txt('Resource not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $uid)
		     ->set('created', $resource->created)
		     ->set('created_by', $resource->created_by)
		     ->set('title', $resource->title)
		     ->set('description', $resource->abstract)
		     ->set('url', Route::url('index.php?option=com_publications&id=' . $id . '&v=' . $v));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
