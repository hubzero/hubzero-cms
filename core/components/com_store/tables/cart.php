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

namespace Components\Store\Tables;
/**
 * Table class for store cart
 */
class Cart extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__cart', 'id', $db);
	}

	/**
	 * Check if an item is already in the cart
	 *
	 * @param   integer  $id   Entry ID
	 * @param   integer  $uid  User ID
	 * @return  array
	 */
	public function checkCartItem($id=null, $uid)
	{
		if ($id == null or $uid == null)
		{
			return false;
		}

		$sql = "SELECT id, quantity FROM $this->_tbl WHERE itemid=" . $this->_db->quote($id) . " AND uid=" . $this->_db->quote($uid);
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get items int he cart
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $rtrn  Return cost or items?
	 * @return  mixed
	 */
	public function getCartItems($uid, $rtrn='')
	{
		$total = 0;
		if ($uid == null)
		{
			return false;
		}

		// clean-up items with zero quantity
		$sql = "DELETE FROM $this->_tbl WHERE quantity=0";
		$this->_db->setQuery($sql);
		$this->_db->query();

		$query  = "SELECT B.quantity, B.itemid, B.uid, B.added, B.selections, a.title, a.price, a.available, a.params, a.type, a.category ";
		$query .= " FROM $this->_tbl AS B, #__store AS a";
		$query .= " WHERE a.id = B.itemid AND B.uid=" . $this->_db->quote($uid);
		$query .= " ORDER BY B.id DESC";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		if ($result)
		{
			foreach ($result as $r)
			{
				$price = $r->price * $r->quantity;
				if ($r->available)
				{
					$total = $total + $price;
				}

				$params     = new \Hubzero\Config\Registry($r->params);
				$selections = new \Hubzero\Config\Registry($r->selections);

				// get size selection
				$r->sizes         = $params->get('size', '');
				$r->sizes         = str_replace(' ', '', $r->sizes);
				$r->selectedsize  = trim($selections->get('size', ''));
				$r->sizes         = explode(',', $r->sizes);

				// get color selection
				$r->colors        = $params->get('color', '');
				$r->colors        = str_replace(' ', '', $r->colors);
				$r->selectedcolor = trim($selections->get('color', ''));
				$r->colors        = explode(',', $r->colors);
			}
		}

		if ($rtrn)
		{
			$result = $total; // total cost of items in cart
		}

		return $result;
	}

	/**
	 * Save items to the cart
	 *
	 * @param   array    $posteditems  List of items to save
	 * @param   string   $uid          User ID
	 * @return  boolean  True upon success
	 */
	public function saveCart($posteditems, $uid)
	{
		if ($uid == null)
		{
			return false;
		}

		// get current cart items
		$items = $this->getCartItems($uid);
		if ($items)
		{
			foreach ($items as $item)
			{
				if ($item->type != 2)
				{
					// not service
					$size        = (isset($item->selectedsize)) ? $item->selectedsize : '';
					$color       = (isset($item->color)) ? $item->color : '';
					$sizechoice  = (isset($posteditems['size' . $item->itemid]))  ? $posteditems['size' . $item->itemid]  : $size;
					$colorchoice = (isset($posteditems['color' . $item->itemid])) ? $posteditems['color' . $item->itemid] : $color;
					$newquantity = (isset($posteditems['num' . $item->itemid]))   ? $posteditems['num' . $item->itemid]   : $item->quantity;

					$selection  = '';
					$selection .= 'size=';
					$selection .= $sizechoice;
					$selection .= "\n";
					$selection .= 'color=';
					$selection .= $colorchoice;

					$query  = "UPDATE $this->_tbl SET quantity=" . $this->_db->quote($newquantity) . ",";
					$query .= " selections=" . $this->_db->quote($selection);
					$query .= " WHERE itemid=" . $this->_db->quote($item->itemid);
					$query .= " AND uid=" . $this->_db->quote($uid);
					$this->_db->setQuery($query);
					$this->_db->query();
				}
			}
		}
	}

	/**
	 * Remove an item from the cart
	 *
	 * @param   integer  $id   Entry ID
	 * @param   integer  $uid  User ID
	 * @param   integer  $all  Remove all items?
	 * @return  void
	 */
	public function deleteCartItem($id, $uid, $all=0)
	{
		$sql = "DELETE FROM $this->_tbl WHERE uid=" . $this->_db->quote($uid);
		if (!$all && $id)
		{
			$sql .= " AND itemid=" . $this->_db->quote($id);
		}

		$this->_db->setQuery($sql);
		$this->_db->query();
	}

	/**
	 * Delete items marked as unavailable
	 *
	 * @param   integer  $uid    User ID
	 * @param   array    $items  List of item IDs
	 * @return  boolean  True upon success
	 */
	public function deleteUnavail($uid, $items)
	{
		if ($uid == null)
		{
			return false;
		}
		if (count($items) > 0)
		{
			foreach ($items as $i)
			{
				if ($i->available == 0)
				{
					$sql = "DELETE FROM $this->_tbl WHERE itemid=" . $this->_db->quote($i->itemid) . " AND uid=" . $this->_db->quote($uid);
					$this->_db->setQuery($sql);
					$this->_db->query();
				}
			}
		}
	}

	/**
	 * Delete an entry
	 *
	 * @param   integer  $itemid  Entry ID
	 * @param   integer  $uid     User ID
	 * @param   string   $type    Entry type
	 * @return  boolean  True upon success
	 */
	public function deleteItem($itemid=null, $uid=null, $type='merchandise')
	{
		if ($itemid == null)
		{
			return false;
		}
		if ($uid == null)
		{
			return false;
		}

		$sql = "DELETE FROM $this->_tbl WHERE itemid=" . $this->_db->quote($itemid) . " AND type=" . $this->_db->quote($type) . " AND uid=" . $this->_db->quote($uid);
		$this->_db->setQuery($sql);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}

