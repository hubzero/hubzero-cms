<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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

		if ($category == 'pubreview')
		{
			$query  = "SELECT rr.id, rr.comment as text, rr.created, rr.created_by as author,
						NULL as subject, 'pubreview' as parent_category, rr.anonymous as anon
						FROM #__publication_ratings AS rr
						WHERE rr.id=" . $refid;
		}
		else if ($category == 'pubreviewcomment')
		{
			$query  = "SELECT rr.id, rr.content as text, rr.created, rr.created_by as author,
						NULL as subject, 'pubreviewcomment' as parent_category, rr.anonymous as anon
						FROM #__item_comments AS rr
						WHERE rr.id=" . $refid;
		}

		$database = JFactory::getDBO();
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
				$rows[$key]->href = ($parent) ? JRoute::_('index.php?option=com_publications&id=' . $parent . '&active=reviews') : '';
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
		$database = JFactory::getDBO();
		$refid = $parentid;

		if ($category == 'pubreviewcomment')
		{
			$pdata = $this->parent($parentid);
			$category = $pdata->item_type;
			$refid = $pdata->item_id;

			if ($pdata->category == 'pubreviewcomment')
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
			}
		}

		if ($category == 'review')
		{
			$database->setQuery("SELECT publication_id FROM #__publication_ratings WHERE id=" . $refid);
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
		$database = JFactory::getDBO();

		$parent = new \Hubzero\Item\Comment($database);
		$parent->load($parentid);

		return $parent;
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
				return JText::sprintf('PLG_SUPPORT_PUBLICATIONS_REVIEW_OF', $parentid);
			break;

			case 'pubreviewcomment':
				return JText::sprintf('PLG_SUPPORT_PUBLICATIONS_COMMENT_OF', $parentid);
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

		$msg = JText::_('PLG_SUPPORT_PUBLICATIONS_CONTENT_FOUND_OBJECTIONABLE');

		$database = JFactory::getDBO();

		switch ($category)
		{
			case 'review':
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'publication.php');
				include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_publications' . DS . 'tables' . DS . 'review.php');

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

				$message .= JText::sprintf('PLG_SUPPORT_PUBLICATIONS_NOTIFICATION_OF_REMOVAL', $parentid);
			break;

			case 'reviewcomment':
				$comment = new \Hubzero\Item\Comment($database);
				$comment->load($referenceid);
				//$comment->state = 2;
				if (preg_match('/^<!-- \{FORMAT:(.*)\} -->/i', $comment->content, $matches))
				{
					$format = strtolower(trim($matches[1]));
					switch ($format)
					{
						case 'html':
							$comment->content = '<!-- {FORMAT:HTML} --><span class="warning">' . $msg . '</span>';
						break;

						case 'wiki':
						default:
							$comment->content = '<!-- {FORMAT:WIKI} -->[[Span(' . $msg . ', class="warning")]]';
						break;
					}
				}
				else
				{
					$comment->content = '[[Span(' . $msg . ', class="warning")]]';
				}

				if (!$comment->store())
				{
					$this->setError($comment->getError());
					return false;
				}

				$message .= JText::sprintf('PLG_SUPPORT_PUBLICATIONS_NOTIFICATION_OF_REMOVAL', $parentid);
			break;
		}

		return $message;
	}
}
