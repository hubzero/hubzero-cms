<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Hubzero\Item;

/**
 * Table class for comment votes
 */
class Vote extends \JTable
{
	/**
	 * Primary key
	 *
	 * @var integer int(11)
	 */
	var $id         = NULL;

	/**
	 * Object this is a comment for
	 *
	 * @var integer int(11)
	 */
	var $item_id    = NULL;

	/**
	 * Object type (resource, kb, etc)
	 *
	 * @var string varchar(100)
	 */
	var $item_type  = NULL;

	/**
	 * IP address
	 *
	 * @var string varchar(100)
	 */
	var $ip         = NULL;

	/**
	 * When the entry was created
	 *
	 * @var string datetime (0000-00-00 00:00:00)
	 */
	var $created    = NULL;

	/**
	 * Who created this entry
	 *
	 * @var integer int(11)
	 */
	var $created_by = NULL;

	/**
	 * Vote
	 *
	 * @var integer int(11)
	 */
	var $vote       = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_votes', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(\JText::_('Missing item ID.'));
			return false;
		}

		$this->item_type = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($this->item_type)));
		if (!$this->item_type)
		{
			$this->setError(\JText::_('Missing item type.'));
			return false;
		}

		if (!$this->created_by)
		{
			$juser = \JFactory::getUser();
			$this->created_by = $juser->get('id');
		}

		switch ($this->vote)
		{
			case 'down':
			case 'dislike':
			case 'negative':
			case 'minus':
			case '-':
			case '-1':
				$this->vote = -1;
			break;

			case 'up':
			case 'like':
			case 'positive':
			case 'plus':
			case '+':
			case '1':
			default:
				$this->vote = 1;
			break;
		}

		if (!$this->id)
		{
			$this->created = \JFactory::getDate()->toSql();
		}

		return true;
	}
}
