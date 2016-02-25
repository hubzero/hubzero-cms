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
 * Support plugin class for com_wishlist entries
 */
class plgSupportWishlist extends \Hubzero\Plugin\Plugin
{
	/**
	 * Is the category one this plugin handles?
	 *
	 * @param   string  $category  Element type (determines table to look in)
	 * @return  boolean
	 */
	private function _canHandle($category)
	{
		if (in_array($category, array('wish', 'wishcomment')))
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

		$database = App::get('db');

		if ($category == 'wish')
		{
			$query  = "SELECT ws.id, ws.about as `text`, ws.proposed AS created, ws.proposed_by as `author`, ws.subject as `subject`, 'wish' as parent_category, ws.anonymous as anon
						FROM `#__wishlist_item` AS ws
						WHERE ws.id=" . $database->quote($refid);
		}
		else if ($category == 'wishcomment')
		{
			$query  = "SELECT rr.id, rr.content as `text`, rr.created, rr.created_by as `author`, NULL as `subject`, rr.category as parent_category, rr.anonymous as anon
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
				$rows[$key]->href = ($parent) ? Route::url('index.php?option=com_wishlist&task=wishlist&id=' . $parent) : '';
				if ($rows[$key]->parent_category == 'wishcomment')
				{
					$rows[$key]->href = Route::url('index.php?option=com_wishlist&task=wish&wishid=' . $parent);
				}
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

		if ($category == 'wishcomment')
		{
			$pdata = $this->parent($parentid);

			$refid    = $pdata->get('item_id');
			$category = 'wish';
			/*$category = $pdata->get('category');
			$refid    = $pdata->get('referenceid');

			if ($pdata->get('category') == 'wishcomment')
			{
				// Yet another level?
				$pdata = $this->parent($pdata->referenceid);
				$category = $pdata->get('category');
				$refid    = $pdata->get('referenceid');

				if ($pdata->get('category') == 'wishcomment')
				{
					// Yet another level?
					$pdata = $this->parent($pdata->referenceid);
					$category = $pdata->get('category');
					$refid    = $pdata->get('referenceid');
				}
			}*/
		}

		if ($category == 'wish')
		{
			$database->setQuery("SELECT wishlist FROM `#__wishlist_item` WHERE id=" . $refid);
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
			case 'wish':
				return Lang::txt('PLG_SUPPORT_WISHLIST_WISH_OF', $parentid);
			break;

			case 'wishcomment':
				return Lang::txt('PLG_SUPPORT_WISHLIST_COMMENT_OF', $parentid);
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

		$database = App::get('db');

		switch ($category)
		{
			case 'wish':
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish.php');

				$wish = new \Components\Wishlist\Tables\Wish($database);
				$wish->load($refid);
				$wish->status = 7;
				$wish->store();
			break;

			case 'wishcomment':
				$comment = \Hubzero\Item\Comment::oneOrFail($refid);
				$comment->set('state', 3);
				$comment->save();
			break;
		}

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

		$database = App::get('db');

		switch ($category)
		{
			case 'wish':
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish.php');

				$wish = new \Components\Wishlist\Tables\Wish($database);
				$wish->load($refid);
				$wish->status = 0;
				$wish->store();
			break;

			case 'wishcomment':
				$comment = \Hubzero\Item\Comment::oneOrFail($refid);
				$comment->set('state', $comment::STATE_PUBLISHED);
				$comment->save();
			break;
		}

		return '';
	}

	/**
	 * Removes an item reported as abusive
	 *
	 * @param   integer  $referenceid  ID of the database table row
	 * @param   integer  $parentid     If the element has a parent element
	 * @param   string   $category     Element type (determines table to look in)
	 * @param   string   $message      Message to user to append to
	 * @return  string
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
			case 'wish':
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wishlist.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish' . DS . 'plan.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'owner.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'ownergroup.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish' . DS . 'rank.php');
				include_once(PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'tables' . DS . 'wish' . DS . 'attachment.php');

				// Delete the wish
				$wish = new \Components\Wishlist\Tables\Wish($database);
				$wish->delete_wish($referenceid);

				// also delete all votes for this wish
				$objR = new \Components\Wishlist\Tables\Wish\Rank($database);
				$objR->remove_vote($referenceid);

				$message .= Lang::txt('PLG_SUPPORT_WISHLIST_NOTIFICATION_OF_WISH_REMOVAL', $parentid);
			break;

			case 'wishcomment':
				$comment = \Hubzero\Item\Comment::oneOrFail($referenceid);
				$comment->set('state', $comment::STATE_DELETED);

				if (!$comment->save())
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= Lang::txt('PLG_SUPPORT_WISHLIST_NOTIFICATION_OF_COMMENT_REMOVAL', $parentid);
			break;
		}

		return $message;
	}
}
