<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Resources\Models\Entry;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for an item
 */
class Resources extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'resource';

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
	 * Chck if we're on a URL where an item can be collected
	 *
	 * @return  boolean
	 */
	public function canCollect()
	{
		if (Request::getCmd('option') != 'com_resources')
		{
			return false;
		}

		if (!Request::getInt('id', 0))
		{
			if (!Request::getString('alias', ''))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Create an item entry for a resource
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

		include_once \Component::path('com_resources') . DS . 'models' . DS . 'entry.php';
		$resource = null;

		if (!$id)
		{
			$alias = Request::getString('alias', '');

			$resource = Entry::oneByAlias($alias);
			$id = $resource->id;
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$resource)
		{
			$resource = Entry::oneOrFail($id);
		}

		if (!$resource->id)
		{
			$this->setError(Lang::txt('Resource not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $resource->id)
		     ->set('created', $resource->created)
		     ->set('created_by', $resource->created_by)
		     ->set('title', $resource->title)
		     ->set('description', $resource->introtext)
		     ->set('url', Route::url('index.php?option=com_resources&id=' . $resource->id));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
