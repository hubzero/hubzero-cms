<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Support plugin class for wiki comments
 */
class plgSupportWiki extends \Hubzero\Plugin\Plugin
{
	/**
	 * Is the category one this plugin handles?
	 *
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  boolean
	 */
	private function _canHandle($category)
	{
		if (in_array($category, array('wiki', 'wikicomment')))
		{
			return true;
		}
		return false;
	}

	/**
	 * Retrieves a row from the database
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $category  Element type (determines table to look in)
	 * @param   string  $parent    If the element has a parent element
	 * @return  array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once Component::path('com_wiki') . DS . 'models' . DS . 'page.php';

		$query  = "SELECT rc.id, rc.page_id AS entry_id, rc.chtml as `text`, rc.created_by as author, rc.created, NULL as subject, rc.anonymous as anon, 'wiki' AS parent_category
					FROM `#__wiki_comments` AS rc
					WHERE rc.id=" . $refid;

		$database = App::get('db');
		$database->setQuery($query);

		$rows = $database->loadObjectList();
		if ($rows)
		{
			foreach ($rows as $key => $row)
			{
				if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches))
				{
					$rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
				}

				$entry = \Components\Wiki\Models\Page::oneOrFail($rows[$key]->entry_id);

				$rows[$key]->text = $rows[$key]->text;
				$rows[$key]->href = Route::url($entry->link() . '#c' . $rows[$key]->id);
			}
		}
		return $rows;
	}

	/**
	 * Mark an item as flagged
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  string
	 */
	public function onReportItem($refid, $category)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once Component::path('com_wiki') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Wiki\Models\Comment::oneOrFail($refid);
		$comment->set('state', \Components\Wiki\Models\Comment::STATE_FLAGGED);
		$comment->save();

		return '';
	}

	/**
	 * Release a reported item
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $parent    If the element has a parent element
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  array
	 */
	public function releaseReportedItem($refid, $parent, $category)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once Component::path('com_wiki') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Wiki\Models\Comment::oneOrFail($refid);
		$comment->set('state', \Components\Wiki\Models\Comment::STATE_PUBLISHED);
		$comment->save();

		return '';
	}

	/**
	 * Mark an item as deleted
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $parent    If the element has a parent element
	 * @param   string  $category  Element type (determines table to look in)
	 * @param   string  $message   If the element has a parent element
	 * @return  string
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		require_once Component::path('com_wiki') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Wiki\Models\Comment::oneOrFail($refid);
		$comment->set('state', \Components\Wiki\Models\Comment::STATE_DELETED);
		$comment->save();

		return '';
	}
}
