<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Forum\Models\Post;
use Request;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for an item
 */
class Forum extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'forum';

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
			return Lang::txt('Forum thread');
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
		if (Request::getCmd('option') != 'com_forum')
		{
			return false;
		}

		if (!Request::getInt('thread', 0))
		{
			return false;
		}

		return true;
	}

	/**
	 * Create an item entry for a forum thread
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

		$id = ($id ?: Request::getInt('thread', 0));

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		include_once \Component::path('com_forum') . DS . 'models' . DS . 'post.php';

		$thread = Post::oneOrNew($id);

		if ($thread->isNew())
		{
			$this->setError(Lang::txt('Forum thread not found.'));
			return false;
		}

		$clean = strip_tags($thread->get('comment'));
		$clean = \Hubzero\Utility\Str::truncate($clean, 200);

		$this->set('type', $this->_type)
		     ->set('object_id', $thread->get('id'))
		     ->set('created', $thread->get('created'))
		     ->set('created_by', $thread->get('created_by'))
		     ->set('title', $thread->get('title'))
		     ->set('description', $clean)
		     ->set('url', $thread->link());

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
