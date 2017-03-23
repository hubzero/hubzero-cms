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
 * Support plugin class for transfer
 */
class plgSupportTransfer extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Retrieves a row from the database
	 *
	 * @param   string  $refid     ID of the database table row
	 * @param   string  $category  Element type (determines table to look in)
	 * @param   string  $parent    If the element has a parent element
	 * @return  array
	 */
	public function transferItem($from_type, $from_id, $to_type, $rid=0, $deactivate=1)
	{
		$upconfig = Component::params('com_members');
		$this->banking = $upconfig->get('bankAccounts');

		$database = App::get('db');

		if ($from_type == null or $from_id == null or $to_type == null)
		{
			$this->setError(Lang::txt('PLG_SUPPORT_TRANSFER_ERROR_MISSING_INFO'));
			return false;
		}

		if ($from_type == $to_type)
		{
			$this->setError(Lang::txt('PLG_SUPPORT_TRANSFER_ERROR_CATEGORIES_MUST_BE_DIFFERENT'));
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
		include_once Component::path('com_support') . DS . 'models' . DS . 'ticket.php';
		include_once Component::path('com_answers') . DS . 'models' . DS . 'question.php';
		include_once Component::path('com_wishlist') . DS . 'models' . DS . 'wishlist.php';

		$wconfig = Component::params('com_wishlist');
		$admingroup = $wconfig->get('group') ? $wconfig->get('group') : 'hubadmin';

		// Get needed scripts & initial data
		switch ($from_type)
		{
			// Transfer from a Support Ticket
			case 'ticket':
				$row = new \Components\Support\Models\Ticket($from_id);

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
					$this->setError(Lang::txt('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

			// Transfer from a Question
			case 'question':
				$row = new \Components\Answers\Models\Question($from_id);

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
					$this->setError(Lang::txt('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

			// Transfer from a Wish
			case 'wish':
				$row = \Components\Wishlist\Models\Wish::oneOrFail($from_id);

				if ($row->get('id'))
				{
					$author    = $row->get('proposed_by');
					$subject   = \Hubzero\Utility\String::truncate($row->get('subject'), 200); // max 200 characters
					$body      = $row->get('about');
					$anonymous = $row->get('anonymous');

					// If we are de-activating original item
					if ($deactivate)
					{
						$row->set('status', 2);
						$row->set('ranking', 0);

						// also delete all previous votes for this wish
						$objR = \Components\Wishlist\Models\Rank::oneOrFail($from_id);
						$objR->destroy();
					}

					$wishlist = \Components\Wishlist\Models\Wishlist::oneOrFail($row->wishlist);

					// get owner
					$nativegroups = $wishlist->getOwnergroups($admingroup, 1);
					$owner = (count($nativegroups) > 0 && $nativegroups[0] != $admingroup) ? $nativegroups[0] : ''; // tool group

					if (isset($wishlist->resource) && isset($wishlist->resource->alias))
					{
						$tags  = $wishlist->resource->type == 7 ? 'tool:' : 'resource:';
						$tags .= $wishlist->resource->alias ? $wishlist->resource->alias : $wishlist->referenceid;
					}
				}
				else
				{
					$this->setError(Lang::txt('PLG_SUPPORT_TRANSFER_ERROR_ITEM_NOT_FOUND'));
					return false;
				}
			break;

		}

		// if no author can be found, use current administrator
		$author = User::getInstance($author);
		if (!is_object($author))
		{
			$author = User::getInstance(User::get('id'));
		}

		$today = Date::toSql();

		// Where do we transfer?
		switch ($to_type)
		{
			// Transfer to a Support Ticket
			case 'ticket':
				$newrow = new \Components\Support\Models\Ticket();
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
				$newrow = new \Components\Answers\Models\Question();
				$newrow->set('subject', $subject);
				$newrow->set('question', $body);
				$newrow->set('created', $today);
				$newrow->set('created_by', $author->get('id'));
				$newrow->set('state', 0);
				$newrow->set('anonymous', $anonymous);
			break;

			case 'wish':
				$newrow = \Components\Wishlist\Models\Wish::blank();
				$newrow->set('subject', $subject);
				$newrow->set('about', $body);
				$newrow->set('proposed', $today);
				$newrow->set('proposed_by', $author->get('id'));
				$newrow->set('status', 0);
				$newrow->set('anonymous', $anonymous);

				// which wishlist?
				$mainlist = \Components\Wishlist\Models\Wishlist::oneByReference(1, 'general')->get('id');
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
							$newrow->tag($tags, User::get('id'), 0);
						}
					break;

					case 'question':
						// Tag new question
						if ($tags)
						{
							$newrow->tag($tags, User::get('id'), 0);
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
						$reward = \Hubzero\Bank\Transaction::getAmount('answers', 'hold', $from_id, $author->get('id'));

						// Remove hold
						if ($reward)
						{
							\Hubzero\Bank\Transaction::deleteRecords('answers', 'hold', $from_id);

							// Make credit adjustment
							$BTL_Q = new \Hubzero\Bank\Teller($author->get('id'));
							$credit = $BTL_Q->credit_summary();
							$adjusted = $credit - $reward;
							$BTL_Q->credit_adjustment($adjusted);
						}
					break;

					case 'wish':
						include_once Component::path('com_wishlist') . DS . 'helpers' . DS . 'economy.php';
						$WE = new \Components\Wishlist\Helpers\Economy($database);
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
	 * @param   string  $tag  Tag
	 * @return  mixed   False on error, Integer on success
	 */
	public function getResourceIdFromTag($tag)
	{
		// intended to find a resource from a tag, e.g. tool:cntbands
		if ($tag === null)
		{
			return false;
		}

		$database = App::get('db');
		$database->setQuery('SELECT t.objectid FROM `#__tags_object` as t LEFT JOIN `#__tags` as tt ON tt.id = t.tagid WHERE t.tbl="resources" AND (tt.raw_tag=' . $database->quote($tag) . ' OR tt.tag=' . $database->quote($tag) . ')');
		return $database->loadResult();
	}

	/**
	 * Get a resource ID via a group's alias (CN)
	 *
	 * @param   string  $groupname  Group CN
	 * @return  mixed   False on error, Integer on success
	 */
	public function getResourceIdFromGroup($groupname)
	{
		// intended to find a resource from the name of owner group, e.g. app-cntbands
		if ($groupname === null)
		{
			return false;
		}

		$database = App::get('db');
		$database->setQuery('SELECT r.id FROM `#__resources` as r LEFT JOIN `#__xgroups` as g ON g.cn = r.alias WHERE g.cn=' . $database->quote($groupname));
		return $database->loadResult();
	}
}
