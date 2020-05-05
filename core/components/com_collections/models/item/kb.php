<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Item;

use Components\Collections\Models\Item as GenericItem;
use Components\Kb\Models\Article;
use Components\Kb\Models\Category;
use Request;
use Route;
use Lang;

require_once dirname(__DIR__) . DS . 'item.php';

/**
 * Collections model for a Knowledge base article
 */
class Kb extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'kb';

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
			return Lang::txt('Knowledge base article');
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
		if (Request::getCmd('option') != 'com_kb')
		{
			return false;
		}

		if (Request::getCmd('task') != 'article')
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

		include_once \Component::path('com_kb') . DS . 'models' . DS . 'article.php';
		$article = null;

		if (!$id)
		{
			$category = Category::all()
				->whereEquals('alias', Request::getString('category'))
				->limit(1)
				->row();

			if (!$category->get('id'))
			{
				return true;
			}

			$article = Article::all()
				->whereEquals('alias', Request::getString('alias', ''))
				->whereEquals('category', $category->get('id'))
				->limit(1)
				->row();
			$id = $article->get('id');
		}

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		if (!$article)
		{
			$article = Article::oneOrNew($id);
		}

		if ($article->isNew())
		{
			$this->setError(Lang::txt('Knowledge base article not found.'));
			return false;
		}

		$this->set('type', $this->_type)
		     ->set('object_id', $article->get('id'))
		     ->set('created', $article->get('created'))
		     ->set('created_by', $article->get('created_by'))
		     ->set('title', $article->get('title'))
		     ->set('description', \Hubzero\Utility\Str::truncate($article->fulltxt(), 200))
		     ->set('url', Route::url($article->link()));

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
