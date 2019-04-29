<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Support plugin class for comments
 */
class plgSupportComments extends \Hubzero\Plugin\Plugin
{
	/**
	 * Retrieves a row from the database
	 *
	 * @param   string $refid    ID of the database table row
	 * @param   string $category Element type (determines table to look in)
	 * @param   string $parent   If the element has a parent element
	 * @return  array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$query  = "SELECT rc.`id`, rc.`content` as `text`, rc.`created_by` as `author`, rc.`created`, NULL as `subject`, rc.`anonymous` as `anon`, concat(rc.`item_type`, 'comment') AS `parent_category`, NULL AS `href` "
				. "FROM #__item_comments AS rc "
				. "WHERE rc.id=" . $refid;
		$database = App::get('db');
		$database->setQuery($query);

		if ($rows = $database->loadObjectList())
		{
			if ($parent)
			{
				foreach ($rows as $key => $row)
				{
					if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $row->text, $matches))
					{
						$rows[$key]->text = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $row->text);
					}

					switch ($row->parent_category)
					{
						case 'collection':
							$rows[$key]->href = Route::url('index.php?option=com_collections&controller=posts&post=' . $parent);
						break;

						case 'coursescomment':
							require_once Component::path('com_courses') . DS . 'models' . DS . 'course.php';
							$course = \Components\Courses\Models\Course::getInstance($parent);
							$rows[$key]->href = Route::url($course->link() . '&active=reviews');
						break;

						case 'citations':
						case 'citationscomment':
							$rows[$key]->href = Route::url('index.php?option=com_citations&task=view&id=' . $parent . '&area=reviews');
						break;

						case 'review':
						case 'reviewcomment':
							$rows[$key]->href = Route::url('index.php?option=com_resources&id=' . $parent . '&active=reviews');
						break;

						case 'pubreview':
						case 'pubreviewcomment':
							$rows[$key]->href = Route::url('index.php?option=com_publications&id=' . $parent . '&active=reviews');
						break;

						case 'answer':
						case 'answercomment':
							$rows[$key]->href = Route::url('index.php?option=com_answers&task=question&id=' . $parent);
						break;

						case 'wish':
						case 'wishcomment':
							$rows[$key]->href = Route::url('index.php?option=com_wishlist&task=wish&wishid=' . $parent);
						break;
					}
				}
			}
		}
		return $rows;
	}

	/**
	 * Retrieves a row from the database?
	 *
	 * @param   string $refid    ID of the database table row
	 * @param   string $category Element type (determines table to look in)
	 * @return  string
	 */
	public function onReportItem($refid, $category)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$comment = \Hubzero\Item\Comment::oneOrFail($refid);
		$comment->set('state', 3);
		$comment->save();

		return '';
	}

	/**
	 * Release a reported item
	 *
	 * @param   string $refid    ID of the database table row
	 * @param   string $parent   If the element has a parent element
	 * @param   string $category Element type (determines table to look in)
	 * @return  array
	 */
	public function releaseReportedItem($refid, $parent, $category)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$comment = \Hubzero\Item\Comment::oneOrFail($refid);
		$comment->set('state', $comment::STATE_PUBLISHED);
		$comment->save();

		return '';
	}

	/**
	 * Retrieves a row from the database
	 *
	 * @param   string $refid    ID of the database table row
	 * @param   string $parent   If the element has a parent element
	 * @param   string $category Element type (determines table to look in)
	 * @param   string $message  If the element has a parent element
	 * @return  array
	 */
	public function deleteReportedItem($refid, $parent, $category, $message)
	{
		if (!in_array($category, array('wishcomment', 'answercomment', 'reviewcomment', 'citations', 'citationscomment', 'collection', 'itemcomment', 'coursescomment')))
		{
			return null;
		}

		$this->loadLanguage();

		$msg = Lang::txt('PLG_SUPPORT_COMMENTS_CONTENT_FOUND_OBJECTIONABLE');

		$comment = \Hubzero\Item\Comment::oneOrFail($refid);

		if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $comment->get('content'), $matches))
		{
			$format = strtolower(trim($matches[1]));
			switch ($format)
			{
				case 'html':
					$comment->set('content', '<!-- {FORMAT:HTML} --><span class="warning">' . $msg . '</span>');
				break;

				case 'wiki':
				default:
					$comment->set('content', '<!-- {FORMAT:WIKI} -->[[Span(' . $msg . ', class="warning")]]');
				break;
			}
		}
		else
		{
			$comment->set('content', '[[Span(' . $msg . ', class="warning")]]');
		}
		$comment->set('state', \Hubzero\Item\Comment::STATE_PUBLISHED);
		$comment->save();

		return '';
	}
}
