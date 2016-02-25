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
 * Support plugin class for com_resources entries
 */
class plgSupportPublications extends \Hubzero\Plugin\Plugin
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
		if ($category != 'pubreview' && $category != 'pubreviewcomment')
		{
			return null;
		}

		$database = App::get('db');

		if ($category == 'pubreview')
		{
			$query  = "SELECT rr.id, rr.comment as text, rr.created, rr.created_by as author,
						NULL as subject, 'pubreview' as parent_category, rr.anonymous as anon
						FROM `#__publication_ratings` AS rr
						WHERE rr.id=" . $database->quote($refid);
		}
		else if ($category == 'pubreviewcomment')
		{
			$query  = "SELECT rr.id, rr.content as text, rr.created, rr.created_by as author,
						NULL as subject, 'pubreviewcomment' as parent_category, rr.anonymous as anon
						FROM `#__item_comments` AS rr
						WHERE rr.id=" . $database->quote($refid);
		}

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
				$rows[$key]->href = ($parent) ? Route::url('index.php?option=com_publications&id=' . $parent . '&active=reviews') : '';
			}
		}
		return $rows;
	}

	/**
	 * Looks up ancestors to find root element
	 *
	 * @param      integer $parentid ID to check for parents of
	 * @param      string  $category Element type (determines table to look in)
	 * @return     integer
	 */
	public function getParentId($parentid, $category)
	{
		$database = App::get('db');
		$refid = $parentid;

		if ($category == 'pubreviewcomment')
		{
			$pdata = $this->parent($parentid);
			$category = $pdata->get('item_type');
			$refid    = $pdata->get('item_id');

			/*if ($pdata->category == 'pubreviewcomment')
			{
				// Yet another level?
				$pdata = $this->parent($pdata->parent);
				$category = $pdata->item_type;
				$refid = $pdata->item_id;

				if ($pdata->category == 'pubreviewcomment')
				{
					// Yet another level?
					$pdata = $this->parent($pdata->parent);
					$category = $pdata->item_type;
					$refid = $pdata->item_id;
				}
			}*/
		}

		if ($category == 'review')
		{
			$database->setQuery("SELECT publication_id FROM `#__publication_ratings` WHERE id=" . $refid);
			return $database->loadResult();
		}
	}

	/**
	 * Retrieve parent element
	 *
	 * @param      integer $parentid ID of element to retrieve
	 * @return     object
	 */
	public function parent($parentid)
	{
		return \Hubzero\Item\Comment::oneOrFail($parentid);
	}

	/**
	 * Returns the appropriate text for category
	 *
	 * @param      string  $category Element type (determines text)
	 * @param      integer $parentid ID of element to retrieve
	 * @return     string
	 */
	public function getTitle($category, $parentid)
	{
		if ($category != 'pubreview' && $category != 'pubreviewcomment')
		{
			return null;
		}

		$this->loadLanguage();

		switch ($category)
		{
			case 'pubreview':
				return Lang::txt('PLG_SUPPORT_PUBLICATIONS_REVIEW_OF', $parentid);
			break;

			case 'pubreviewcomment':
				return Lang::txt('PLG_SUPPORT_PUBLICATIONS_COMMENT_OF', $parentid);
			break;
		}
	}

	/**
	 * Removes an item reported as abusive
	 *
	 * @param      integer $referenceid ID of the database table row
	 * @param      integer $parentid    If the element has a parent element
	 * @param      string  $category    Element type (determines table to look in)
	 * @param      string  $message     Message to user to append to
	 * @return     string
	 */
	public function deleteReportedItem($referenceid, $parentid, $category, $message)
	{
		if ($category != 'pubreview' && $category != 'pubreviewcomment')
		{
			return null;
		}

		$this->loadLanguage();

		$msg = Lang::txt('PLG_SUPPORT_PUBLICATIONS_CONTENT_FOUND_OBJECTIONABLE');

		$database = App::get('db');

		switch ($category)
		{
			case 'review':
				include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'review.php');

				// Delete the review
				$review = new PublicationReview($database);
				$review->load($referenceid);
				//$comment->anonymous = 1;
				if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $review->comment, $matches))
				{
					$format = strtolower(trim($matches[1]));
					switch ($format)
					{
						case 'html':
							$review->comment = '<!-- {FORMAT:HTML} --><span class="warning">' . $msg . '</span>';
						break;

						case 'wiki':
						default:
							$review->comment = '<!-- {FORMAT:WIKI} -->[[Span(' . $msg . ', class="warning")]]';
						break;
					}
				}
				else
				{
					$review->comment = '[[Span(' . $msg . ', class="warning")]]';
				}
				$review->store();

				// Recalculate the average rating for the parent resource
				$pub = new Publication($database);
				$pub->load($parentid);
				$pub->calculateRating();
				$pub->updateRating();
				if (!$pub->store())
				{
					$this->setError($pub->getError());
					return false;
				}

				$message .= Lang::txt('PLG_SUPPORT_PUBLICATIONS_NOTIFICATION_OF_REMOVAL', $parentid);
			break;

			case 'reviewcomment':
				$comment = \Hubzero\Item\Comment::oneOrFail($referenceid);

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

				if (!$comment->save())
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= Lang::txt('PLG_SUPPORT_PUBLICATIONS_NOTIFICATION_OF_REMOVAL', $parentid);
			break;
		}

		return $message;
	}
}
