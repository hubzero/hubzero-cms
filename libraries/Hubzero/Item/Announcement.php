<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Item;

use Hubzero\Plugin\View;
use Hubzero\Mail\Message;
use Hubzero\User\Profile;
use Hubzero\User\Group;

/**
 * Hubzero Announcement Model Class
 */
class Announcement extends \JTable
{
	/*
	 * Define Announcement States
	 */
	const STATE_UNPUBLISHED = 0;
	const STATE_PUBLISHED   = 1;
	const STATE_DELETED     = 2;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * varchar(100)
	 * 
	 * @var string
	 */
	var $scope = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $scope_id = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $content = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $priority = NULL;

	/**
	 * datetime
	 * 
	 * @var date
	 */
	var $created = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $created_by = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $state = NULL;

	/**
	 * datetime
	 * 
	 * @var date
	 */
	var $publish_up = NULL;

	/**
	 * datetime
	 * 
	 * @var date
	 */
	var $publish_down = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $sticky = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $email = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $sent = NULL;

	/**
	 * Constructor method for JTable class
	 * 
	 * @param  database object
	 * @return void
	 */
	public function __construct($db)
	{
		parent::__construct('#__announcements', 'id', $db);
	}

	/**
	 * Overloaded Check method. Verify we have all needed things to save
	 */
	public function check()
	{
		//make sure we have content
		if (!isset($this->content) || $this->content == '')
		{
			$this->setError(\JText::_('Announcement must contain some content.'));
			return false;
		}

		if (!$this->created)
		{
			$this->created = \JFactory::getDate()->toSql();
		}

		return true;
	}

	/**
	 * Mark item as archived
	 * 
	 * @return object
	 */
	public function archive()
	{
		$this->state = self::STATE_DELETED;

		return $this;
	}

	/**
	 * Method to check if announcement belongs to entity
	 */
	public function belongsToObject($entity_scope, $entity_id)
	{
		//make sure we have an id
		if (!isset($this->id) || $this->id == null || $this->id == 0)
		{
			return true;
		}

		//make sure scope and id match
		if ($this->scope == $entity_scope && $this->scope_id == $entity_id)
		{
			return true;
		}
		return false;
	}

	/**
	 * Get Announcement Count
	 * 
	 * @param    array    $filters
	 */
	public function count($filters = array())
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get Announcement Records
	 * 
	 * @param    array    $filters
	 */
	public function find($filters = array())
	{
		$query  = "SELECT a.*";
		$query .= $this->_buildQuery($filters);

		//$query .= " ORDER BY a.priority DESC, a.created DESC";
		$query .= " ORDER BY a.created DESC";
		if (isset($filters['limit']))
		{
			if (!isset($filters['start']))
			{
				$filters['start'] = 0;
			}
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Build Query to get Announcements
	 * 
	 * @param    array    $filters
	 */
	private function _buildQuery($filters = array())
	{
		//array to hold where statements
		$where = array();

		//start query
		$query = " FROM $this->_tbl AS a";

		//apply filters based on filters passed in
		if (isset($filters['scope']) && $filters['scope'])
		{
			$where[] = "a.`scope` = " . $this->_db->Quote($filters['scope']);
		}
		if (isset($filters['scope_id']) && $filters['scope_id'])
		{
			$where[] = "a.`scope_id` = " . $this->_db->Quote(intval($filters['scope_id']));
		}
		if (isset($filters['state']) && $filters['state'])
		{
			$where[] = "a.`state` = " . $this->_db->Quote(intval($filters['state']));
		}
		if (isset($filters['created_by']) && $filters['created_by'])
		{
			$where[] = "a.`created_by` = " . $this->_db->Quote(intval($filters['created_by']));
		}
		if (isset($filters['priority']) && $filters['priority'])
		{
			$where[] = "a.`priority` = " . $this->_db->Quote(intval($filters['priority']));
		}
		if (isset($filters['sticky']) && in_array($filters['sticky'], array(0,1)))
		{
			$where[] = "a.`sticky` = " . $this->_db->Quote(intval($filters['sticky']));
		}
		if (isset($filters['email']) && in_array($filters['email'], array(0,1)))
		{
			$where[] = "a.`email` = " . $this->_db->Quote(intval($filters['email']));
		}
		if (isset($filters['sent']) && in_array($filters['sent'], array(0,1)))
		{
			$where[] = "a.`sent` = " . $this->_db->Quote(intval($filters['sent']));
		}

		//published
		if (isset($filters['published']))
		{
			$now = \JFactory::getDate();
			$where[] = "(a.`publish_up` = '0000-00-00 00:00:00' OR a.`publish_up` <= " . $this->_db->Quote($now->toSql()) . ")";
			$where[] = "(a.`publish_down` = '0000-00-00 00:00:00' OR a.`publish_down` >= " . $this->_db->Quote($now->toSql()) . ")";
		}

		//search
		if (isset($filters['search']) && $filters['search'])
		{
			if (is_numeric($filters['search']))
			{
				$where[] = "a.`id`=" . $this->_db->Quote(intval($filters['search']));
			}
			else
			{
				$where[] = "(LOWER(a.content) LIKE '%" . $this->_db->getEscaped(strtolower($filters['search'])) . "%')";
			}
		}

		//if we have an wheres append them
		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(' AND ', $where);
		}

		return $query;
	}

	/**
	 * Check if date is within announcement publish up/down
	 * 
	 * @param    array    $filters
	 */
	public function announcementPublishedForDate($announcement = null, $date = null)
	{
		// var to hold if announcment is published
		$published = false;

		// make sure we have an announcement
		if ($announcement === null)
		{
			$announcement = $this;
		}

		// make sure we have a date
		if ($date === null)
		{
			$date = time();
		}

		//get up and down times
		$up = $down = null;
		if ($announcement->publish_up != '' && $announcement->publish_up != $this->_db->getNullDate())
		{
			$up = \JFactory::getDate($announcement->publish_up)->toUnix();
		}
		if ($announcement->publish_down != '' && $announcement->publish_down != $this->_db->getNullDate())
		{
			$down = \JFactory::getDate($announcement->publish_down)->toUnix();
		}

		// if we have a null uptime or uptime less then our date
		// and if down is null or downtime is greater then our date
		if (($up == null || $date >= $up) && ($down == null || $date <= $down))
		{
			$published = true;
		}

		return $published;
	}
	
	/**
	 * Email Announcement
	 * 
	 * @param    array    $filters
	 */
	public function emailAnnouncement($announcement = null)
	{
		// make sure we have an announcement
		if ($announcement === null)
		{
			$announcement = $this;
		}

		// load group
		$group = Group::getInstance($announcement->scope_id);

		// get all group members
		$groupMembers = array();
		foreach ($group->get('members') as $member)
		{
			if ($profile = Profile::getInstance($member))
			{
				$groupMembers[$profile->get('email')] = $profile->get('name');
			}
		}

		// create view object
		$eview = new View(
			array(
				'folder'  => 'groups',
				'element' => 'announcements',
				'name'    => 'email',
				'layout'  => 'announcement_plain'
			)
		);

		// plain text
		$eview->announcement = $announcement;
		$plain = $eview->loadTemplate();
		$plain = str_replace("\n", "\r\n", $plain);

		// HTML
		$eview->setLayout('announcement_html');
		$html = $eview->loadTemplate();
		$html = str_replace("\n", "\r\n", $html);

		// set from address
		$jconfig = \JFactory::getConfig();
		$from = array(
			'name'  => $jconfig->getValue('config.sitename') . ' Groups',
			'email' => $jconfig->getValue('config.mailfrom')
		);

		// define subject
		$subject = $group->get('description') . ' Group Announcement';

		// message one member at time to hide emails
		foreach ($groupMembers as $email => $name)
		{
			// create message object
			$message = new Message();

			// set message details and send
			$message->setSubject($subject)
				->addFrom($from['email'], $from['name'])
				->setTo($email, $name)
				->addPart($plain, 'text/plain')
				->addPart($html, 'text/html')
				->send();
		}

		// all good
		return true; 
	}
}
