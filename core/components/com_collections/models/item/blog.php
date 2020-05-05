<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Blog\Models\Entry;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for a blog post
 */
class Blog extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'blog';

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
			return Lang::txt('Blog post');
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
		if (Request::getCmd('option') != 'com_blog')
		{
			return false;
		}

		if (!Request::getString('alias'))
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

		include_once \Component::path('com_blog') . DS . 'models' . DS . 'entry.php';
		$post = null;

		if (!$id)
		{
			$alias = Request::getString('alias', '');

			$post = Entry::oneByScope($alias, 'site', 0);
			$id = $post->get('id');
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$post)
		{
			$post = Entry::oneOrFail($id);
		}

		if (!$post->get('id'))
		{
			$this->setError(Lang::txt('Blog post not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $post->get('id'))
		     ->set('created', $post->get('created'))
		     ->set('created_by', $post->get('created_by'))
		     ->set('title', $post->get('title'))
		     ->set('description', \Hubzero\Utility\Str::truncate(strip_tags($post->content()), 200))
		     ->set('url', Route::url($post->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
