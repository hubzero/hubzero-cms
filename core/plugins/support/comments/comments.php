<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $parent   If the element has a parent element
	 * @return     array
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
							require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php';
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
	 * Retrieves a row from the database
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $message  If the element has a parent element
	 * @return     array
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
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @return     array
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
	 * @param      string $refid    ID of the database table row
	 * @param      string $parent   If the element has a parent element
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $message  If the element has a parent element
	 * @return     array
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
		$comment->set('state', $comment::STATE_PUBLISHED);
		$comment->save();

		return '';
	}
}
