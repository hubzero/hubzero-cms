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
 * Support plugin class for transfer
 */
class plgSupportTransfer extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieves a row from the database
	 *
	 * @param      string $refid    ID of the database table row
	 * @param      string $category Element type (determines table to look in)
	 * @param      string $parent   If the element has a parent element
	 * @return     array
	 */
	public function transferItem($from_type, $from_id, $to_type, $rid=0, $deactivate=1)
	{
		$upconfig = JComponentHelper::getParams('com_members');
		$this->banking = $upconfig->get('bankAccounts');

		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		if ($from_type == NULL or $from_id == NULL or $to_type == NULL)
		{
			$this->setError(JText::_('PLG_SUPPORT_TRANSFER_ERROR_MISSING_INFO'));
			return false;
		}

		if ($from_type == $to_type)
		{
			$this->setError(JText::_('PLG_SUPPORT_TRANSFER_ERROR_CATEGORIES_MUST_BE_DIFFERENT'));
			return false;
		}

		// collectors
		$author    = '';
		$subject   = '';
		$body      = '';
		$tags      = '';
		$owner     = ''; // name of group owning the item
		$anonymous = 0;

		// get needed scripts
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'models' . DS . 'wishlist.php');

		$wconfig = JComponentHelper::getParams('com_wishlist');
		$admingroup = $wconfig->get('group') ? $wconfig->get('group') : 'hubadmin';

		// Get needed scripts & initial data
		switch ($from_type)
		{
			// Transfer from a Support Ticket
			case 'ticket':
				$row = new SupportModelTicket($from_id);

				if ($row->exists())
				{
					$author  = $row->get('login');
					$subject = $row->content('raw', 200); // max 200 characters
					$body    = $row->get('summary');
					$owner   = $row->get('group');

					// If we are de-activating original item
					if ($deactivate)
					{
						$row->set('status', 2);
						$row->set('resolved', 'transfered');
					}

					$tags = $row->tags('string');
				}
				else
				{
					$this->setError(JText::_('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

			// Transfer from a Question
			case 'question':
				$row = new AnswersModelQuestion($from_id);

				if ($row->exists())
				{
					$author     = $row->get('created_by');
					$subject    = $row->subject('raw', 200); // max 200 characters
					$body       = $row->get('question');
					$anonymous  = $row->get('anonymous');

					// If we are de-activating original item
					if ($deactivate)
					{
						$row->set('state', 2);
						$row->set('reward', 0);
					}

					$tags = $row->tags('string');
				}
				else
				{
					$this->setError(JText::_('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

			// Transfer from a Wish
			case 'wish':
				$row = new Wish($database);
				$row->load($from_id);

				if ($row->id)
				{
					$author    = $row->proposed_by;
					$subject   = \Hubzero\Utility\String::truncate($row->subject, 200); // max 200 characters
					$body      = $row->about;
					$anonymous = $row->anonymous;

					// If we are de-activating original item
					if ($deactivate)
					{
						$row->status = 2;
						$row->ranking = 0;

						// also delete all previous votes for this wish
						$objR = new WishRank($database);
						$objR->remove_vote($from_id);
					}

					// get owner
					$objG = new WishlistOwnerGroup($database);
					$nativegroups = $objG->get_owner_groups($row->wishlist, $admingroup, '',1);
					$owner = (count($nativegroups) > 0 && $nativegroups[0] != $admingroup) ? $nativegroups[0] : ''; // tool group

					$objWishlist = new Wishlist($database);
					$wishlist = $objWishlist->get_wishlist($row->wishlist);
					if (isset($wishlist->resource) && isset($wishlist->resource->alias))
					{
						$tags  = $wishlist->resource->type == 7 ? 'tool:' : 'resource:';
						$tags .= $wishlist->resource->alias ? $wishlist->resource->alias : $wishlist->referenceid ;
					}
				}
				else
				{
					$this->setError(JText::_('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

		}

		// if no author can be found, use current administrator
		$author = JUser::getInstance($author);
		if (!is_object($author))
		{
			$author = JUser::getInstance($juser->get('id'));
		}

		$today = JFactory::getDate()->toSql();

		// Where do we transfer?
		switch ($to_type)
		{
			// Transfer to a Support Ticket
			case 'ticket':
				$newrow = new SupportModelTicket();
				$newrow->set('open', 1);
				$newrow->set('status', 0);
				$newrow->set('created', $today);
				$newrow->set('login', $author->get('username'));
				$newrow->set('severity', 'normal');
				$newrow->set('summary', $subject);
				$newrow->set('report', ($body ? $body : $subject));
				$newrow->set('section', 1);
				$newrow->set('type', 0);
				$newrow->set('instances', 1);
				$newrow->set('email', $author->get('email'));
				$newrow->set('name', $author->get('name'));

				// do we have an owner group?
				$newrow->set('group', ($owner ? $owner : ''));
			break;

			case 'question':
				$newrow = new AnswersModelQuestion();
				$newrow->set('subject', $subject);
				$newrow->set('question', $body);
				$newrow->set('created', $today);
				$newrow->set('created_by', $author->get('id'));
				$newrow->set('state', 0);
				$newrow->set('anonymous', $anonymous);
			break;

			case 'wish':
				$newrow = new WishlistModelWish();
				$newrow->set('subject', $subject);
				$newrow->set('about', $body);
				$newrow->set('proposed', $today);
				$newrow->set('proposed_by', $author->get('id'));
				$newrow->set('status', 0);
				$newrow->set('anonymous', $anonymous);

				// which wishlist?
				$objWishlist = new Wishlist($database);
				$mainlist = $objWishlist->get_wishlistID(1, 'general');
				$listid = 0;
				if (!$rid && $owner)
				{
					$rid = $this->getResourceIdFromGroup($owner);
				}

				if ($rid)
				{
					$listid = $objWishlist->get_wishlistID($rid);
				}
				$newrow->set('wishlist', ($listid ? $listid : $mainlist));
			break;
		}

		// Save new information
		if (!$newrow->store())
		{
			$this->setError($newrow->getError());
			return;
		}
		else
		{
			// Checkin ticket
			//$newrow->checkin();

			// Extras
			if ($newrow->exists())
			{
				switch ($to_type)
				{
					case 'ticket':
						// Tag new ticket
						if ($tags)
						{
							$newrow->tag($tags, $juser->get('id'), 0);
						}
					break;

					case 'question':
						// Tag new question
						if ($tags)
						{
							$newrow->tag($tags, $juser->get('id'), 0);
						}
					break;
				}
			}
		}

		// If we are de-activating original item
		if ($deactivate)
		{
			// overwrite old entry
			if (!$row->store())
			{
				$this->setError($row->getError());
				exit();
			}

			// Clean up rewards if banking
			if ($this->banking)
			{
				switch ($from_type)
				{
					case 'ticket':
						// no banking yet
					break;

					case 'question':
						$BT = new \Hubzero\Bank\Transaction($database);
						$reward = $BT->getAmount('answers', 'hold', $from_id, $author->get('id'));

						// Remove hold
						if ($reward)
						{
							$BT->deleteRecords('answers', 'hold', $from_id);

							// Make credit adjustment
							$BTL_Q = new \Hubzero\Bank\Teller($database, $author->get('id'));
							$credit = $BTL_Q->credit_summary();
							$adjusted = $credit - $reward;
							$BTL_Q->credit_adjustment($adjusted);
						}
					break;

					case 'wish':
						include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wishlist' . DS . 'helpers' . DS . 'economy.php');
						$WE = new WishlistEconomy($database);
						$WE->cleanupBonus($from_id);
					break;
				}
			}
		}

		return $newrow->get('id');
	}

	/**
	 * Get a resource ID via a tag
	 *
	 * @param      string $tag Tag
	 * @return     mixed  False on error, Integer on success
	 */
	public function getResourceIdFromTag($tag)
	{
		// intended to find a resource from a tag, e.g. tool:cntbands
		if ($tag === NULL)
		{
			return false;
		}

		$database = JFactory::getDBO();
		$database->setQuery('SELECT t.objectid FROM `#__tags_object` as t LEFT JOIN `#__tags` as tt ON tt.id = t.tagid WHERE t.tbl="resources" AND (tt.raw_tag=' . $database->quote($tag) . ' OR tt.tag=' . $database->quote($tag) . ')');
		return $database->loadResult();
	}

	/**
	 * Get a resource ID via a group's alias (CN)
	 *
	 * @param      string $groupname Group CN
	 * @return     mixed  False on error, Integer on success
	 */
	public function getResourceIdFromGroup($groupname)
	{
		// intended to find a resource from the name of owner group, e.g. app-cntbands
		if ($groupname === NULL)
		{
			return false;
		}

		$database = JFactory::getDBO();
		$database->setQuery('SELECT r.id FROM `#__resources` as r LEFT JOIN `#__xgroups` as g ON g.cn = r.alias WHERE g.cn=' . $database->quote($groupname));
		return $database->loadResult();
	}
}

