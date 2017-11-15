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
class plgSupportResources extends \Hubzero\Plugin\Plugin
{
	/**
	 * Is the category one this plugin handles?
	 *
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  boolean
	 */
	private function _canHandle($category)
	{
		if (in_array($category, array('review', 'reviewcomment')))
		{
			return true;
		}
		return false;
	}

	/**
	 * Get items reported as abusive
	 *
	 * @param   integer  $refid     Comment ID
	 * @param   string   $category  Item type (kb)
	 * @param   integer  $parent    Parent ID
	 * @return  array
	 */
	public function getReportedItem($refid, $category, $parent)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		$database = App::get('db');

		if ($category == 'review')
		{
			$query  = "SELECT rr.id, rr.comment as `text`, rr.created, rr.user_id as `author`,
						NULL as subject, 'review' as parent_category, rr.anonymous as anon
						FROM `#__resource_ratings` AS rr
						WHERE rr.id=" . $database->quote($refid);
		}
		else if ($category == 'reviewcomment')
		{
			$query  = "SELECT rr.id, rr.content as `text`, rr.created, rr.created_by as `author`,
						NULL as subject, 'reviewcomment' as parent_category, rr.anonymous as anon
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
				$rows[$key]->href = ($parent) ? Route::url('index.php?option=com_resources&id=' . $parent . '&active=reviews') : '';
			}
		}
		return $rows;
	}

	/**
	 * Looks up ancestors to find root element
	 *
	 * @param   integer  $parentid  ID to check for parents of
	 * @param   string   $category  Element type (determines table to look in)
	 * @return  integer
	 */
	public function getParentId($parentid, $category)
	{
		$database = App::get('db');
		$refid = $parentid;

		if ($category == 'reviewcomment')
		{
			$pdata = $this->parent($parentid);

			$refid    = $pdata->get('item_id');
			$category = 'review';
		}

		if ($category == 'review')
		{
			$database->setQuery("SELECT resource_id FROM `#__resource_ratings` WHERE id=" . $refid);
			return $database->loadResult();
		}
	}

	/**
	 * Retrieve parent element
	 *
	 * @param   integer  $parentid  ID of element to retrieve
	 * @return  object
	 */
	public function parent($parentid)
	{
		return \Hubzero\Item\Comment::oneOrFail($parentid);
	}

	/**
	 * Returns the appropriate text for category
	 *
	 * @param   string   $category  Element type (determines text)
	 * @param   integer  $parentid  ID of element to retrieve
	 * @return  string
	 */
	public function getTitle($category, $parentid)
	{
		if (!$this->_canHandle($category))
		{
			return null;
		}

		$this->loadLanguage();

		switch ($category)
		{
			case 'review':
				return Lang::txt('PLG_SUPPORT_RESOURCES_REVIEW_OF', $parentid);
			break;

			case 'reviewcomment':
				return Lang::txt('PLG_SUPPORT_RESOURCES_COMMENT_OF', $parentid);
			break;
		}
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

		$path = DS . 'plugins' . DS . 'resources' . DS . 'reviews' . DS . 'models' . DS . 'review.php';
		if (file_exists(PATH_APP . $path))
		{
			$path = PATH_APP . $path;
		}
		else
		{
			$path = PATH_CORE . $path;
		}

		include_once $path;

		$comment = \Components\Resources\Reviews\Models\Review::oneOrFail($refid);
		$comment->set('state', 3);
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

		$path = DS . 'plugins' . DS . 'resources' . DS . 'reviews' . DS . 'models' . DS . 'review.php';
		if (file_exists(PATH_APP . $path))
		{
			$path = PATH_APP . $path;
		}
		else
		{
			$path = PATH_CORE . $path;
		}

		include_once $path;

		$comment = \Components\Resources\Reviews\Models\Review::oneOrFail($refid);
		$comment->set('state', 1);
		$comment->save();

		return '';
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
		if (!$this->_canHandle($category))
		{
			return null;
		}

		$this->loadLanguage();

		$database = App::get('db');

		switch ($category)
		{
			case 'review':
				$path = DS . 'plugins' . DS . 'resources' . DS . 'reviews' . DS . 'models' . DS . 'review.php';
				if (file_exists(PATH_APP . $path))
				{
					$path = PATH_APP . $path;
				}
				else
				{
					$path = PATH_CORE . $path;
				}

				include_once $path;
				include_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

				// Delete the review
				$review = \Components\Resources\Reviews\Models\Review::oneOrFail($referenceid);
				$review->set('state', 2);
				$review->save();

				$rating = \Components\Resources\Reviews\Models\Review::averageByResource($parentid);

				// Recalculate the average rating for the parent resource
				$resource = \Components\Resources\Models\Entry::oneOrFail($parentid);
				$resource->set('rating', $rating[0]);
				$resource->set('times_rated', $rating[1]);
				if (!$resource->save())
				{
					$this->setError($resource->getError());
					return false;
				}

				$message .= Lang::txt('PLG_SUPPORT_RESOURCES_NOTIFICATION_OF_REMOVAL', $parentid);
			break;

			case 'reviewcomment':
				$comment = \Hubzero\Item\Comment::oneOrFail($referenceid);
				$comment->set('state', $comment::STATE_DELETED);

				if (!$comment->save())
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= Lang::txt('PLG_SUPPORT_RESOURCES_NOTIFICATION_OF_REMOVAL', $parentid);
			break;
		}

		return $message;
	}
}
