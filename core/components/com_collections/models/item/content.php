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
 * Collections model for an item
 */
class Content extends GenericItem
{
	/**
	 * Item type
	 *
	 * @var  string
	 */
	protected $_type = 'article';

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
			return Lang::txt('Article');
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
		if (Request::getCmd('option') != 'com_content')
		{
			return false;
		}

		if (!Request::getInt('id', 0))
		{
			return false;
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

		$this->_tbl->loadType($id, $this->_type);

		if ($this->exists())
		{
			return true;
		}

		include_once \Component::path('com_content') . DS . 'models' . DS . 'article.php';

		$article = \Components\Content\Models\Article::oneOrNew($id);

		if (!$article->id)
		{
			$this->setError(Lang::txt('Article not found.'));
			return false;
		}

		$text = strip_tags($article->introtext);
		$text = str_replace(array("\n", "\r", "\t"), ' ', $text);
		$text = preg_replace('/\s+/', ' ', $text);

		$url = Request::getString('REQUEST_URI', '', 'server');
		$url = ($url ?: Route::url('index.php?option=com_content&id=' . $article->alias));
		$url = str_replace('?tryto=collect', '', $url);
		$url = str_replace('no_html=1', '', $url);
		$url = trim($url, '&');

		$this->set('type', $this->_type)
		     ->set('object_id', $article->id)
		     ->set('created', $article->created)
		     ->set('created_by', $article->created_by)
		     ->set('title', $article->title)
		     ->set('description', Str::truncate($text, 300, array('html' => true)))
		     ->set('url', $url);

		if (!$this->store())
		{
			return false;
		}

		return true;
	}
}
