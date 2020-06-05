<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Plugin for abuse reports on KB comments
 */
class plgSupportKb extends \Hubzero\Plugin\Plugin
{
	/**
	 * Get items reported as abusive
	 *
	 * @param      integer $refid    Comment ID
	 * @param      string  $category Item type (kb)
	 * @param      integer $parent   Parent ID
	 * @return     array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if ($category != 'kb')
		{
			return null;
		}

		$query  = "SELECT rc.id, rc.content as text, rc.created_by as author, rc.created, NULL as subject, rc.anonymous as anon, 'kb' AS parent_category,
					c.alias AS section, c.alias AS category, f.alias AS article
					FROM `#__kb_comments` AS rc
					LEFT JOIN `#__kb_articles` AS f
						ON f.id = rc.entry_id
					LEFT JOIN `#__categories` AS c
						ON c.id = f.category
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
				$rows[$key]->href = Route::url('index.php?option=com_kb&category=' . $row->category . '&alias=' . $row->article);
			}
		}
		return $rows;
	}

	/**
	 * Mark an item as flagged
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @return     string
	 */
	public function onReportItem($refid, $category)
	{
		if ($category != 'kb')
		{
			return null;
		}

		require_once Component::path('com_kb') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Kb\Models\Comment::oneOrFail($refid);
		$comment->set('state', 3);
		$comment->save();

		return '';
	}

	/**
	 * Release a reported item
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @return     array
	 */
	public function releaseReportedItem($refid, $parent, $category)
	{
		if ($category != 'kb')
		{
			return null;
		}

		require_once Component::path('com_kb') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Kb\Models\Comment::oneOrFail($refid);
		$comment->set('state', 1);
		$comment->save();

		return '';
	}

	/**
	 * Retrieves a row from the database
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $message  If the element has a parent element
	 * @return     array
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if ($category != 'kb')
		{
			return null;
		}

		require_once Component::path('com_kb') . DS . 'models' . DS . 'comment.php';

		$comment = \Components\Kb\Models\Comment::oneOrFail($refid);
		$comment->set('state', 2);
		$comment->save();

		return '';
	}
}
