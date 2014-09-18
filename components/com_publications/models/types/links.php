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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * LINKS (external content) master type helper class
 */
class typeLinks extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_database       	= NULL;

	/**
	 * Project
	 *
	 * @var object
	 */
	var $_project      	 	= NULL;

	/**
	 * Base alias
	 *
	 * @var integer
	 */
	var $_base   		 	= 'links';

	/**
	 * Attachment type
	 *
	 * @var string
	 */
	var $_attachmentType 	= 'link';

	/**
	 * Selection type (single/multi)
	 *
	 * @var boolean
	 */
	var $_multiSelect 	 	= false;

	/**
	 * Allow change to selection after draft is started?
	 *
	 * @var boolean
	 */
	var $_changeAllowed  	= false;

	/**
	 * Allow to create a new publication with exact same content?
	 *
	 * @var boolean
	 */
	var $_allowDuplicate  	= false;

	/**
	 * Unique attachment properties
	 *
	 * @var array
	 */
	var $_attProperties  	= array('path', 'object_name');

	/**
	 * Data
	 *
	 * @var array
	 */
	var $_data   		 = array();

	/**
	 * Serve as (default value)
	 *
	 * @var string
	 */
	var $_serveas   	= 'external';

	/**
	 * Serve as choices
	 *
	 * @var string
	 */
	var $_serveChoices  = array('external');

	/**
	 * Constructor
	 *
	 * @param      object  &$db      	 JDatabase
	 * @return     void
	 */
	public function __construct( &$db, $project = NULL, $data = array() )
	{
		$this->_database = $db;
		$this->_project  = $project;
		$this->_data 	 = $data;
	}

	/**
	 * Set
	 *
	 * @param      string 	$property
	 * @param      string 	$value
	 * @return     mixed
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get
	 *
	 * @param      string 	$property
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Dispatch task
	 *
	 * @param      string  $task
	 * @return     void
	 */
	public function dispatch( $task = NULL )
	{
		$output 		 = NULL;

		switch ( $task )
		{
			case 'getServeAs':
				$output = $this->_getServeAs();
				break;

			case 'checkContent':
				$output = $this->_checkContent();
				break;

			case 'checkMissing':
				$output = $this->_checkMissing();
				break;

			case 'drawItem':
				$output = $this->_drawItem();
				break;

			case 'saveAttachments':
				$output = $this->_saveAttachments();
				break;

			case 'cleanupAttachments':
				$output = $this->_cleanupAttachments();
				break;

			case 'getPubTitle':
				$output = $this->_getPubTitle();

			default:
				break;
		}

		return $output;
	}

	/**
	 * Get serveas options (_showOptions function in plg_projects_publications)
	 *
	 * @return     void
	 */
	protected function _getServeAs()
	{
		$result = array('serveas' => $this->_serveas, 'choices' => $this->_serveChoices);

		return $result;
	}

	/**
	 * Get publication title for newly created draft
	 *
	 * @return     void
	 */
	protected function _getPubTitle($title = '')
	{
		// Incoming data
		$item = $this->__get('item');

		// TBD

		return $title;

	}

	/**
	 * Check content
	 *
	 * @return     void
	 */
	protected function _checkContent()
	{
		// Incoming data
		$attachments = $this->__get('attachments');

		if ($attachments && count($attachments) > 0)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check missing content
	 *
	 * @return     void
	 */
	protected function _checkMissing()
	{
		// Incoming data
		$item  	 = $this->__get('item');
		$config  = $this->__get('config');

		if (!$item)
		{
			return false;
		}

		// TBD check if link still works

		return false;
	}

	/**
	 * Draw selected item html
	 *
	 * @return     void
	 */
	protected function _drawItem()
	{
		// Incoming data
		$att   		= $this->__get('att');
		$item   	= $this->__get('item');

		$title   = $att->title ? $att->title : $att->path;
		$subinfo = $att->title ? $att->path : '';

		$html = '<span class="' . $this->_base . '">' . $title . '</span>';
		$html.= '<span class="c-iteminfo">' . $subinfo . '</span>';

		return $html;
	}

	/**
	 * Save picked items as publication attachments
	 *
	 * @return     void
	 */
	protected function _saveAttachments()
	{
		// Incoming data
		$selections 	= $this->__get('selections');
		$option  		= $this->__get('option');
		$vid  			= $this->__get('vid');
		$pid  			= $this->__get('pid');
		$uid  			= $this->__get('uid');
		$update_hash  	= $this->__get('update_hash');
		$primary  		= $this->__get('primary');
		$added  		= $this->__get('added');
		$serveas  		= $this->__get('serveas');
		$state  		= $this->__get('state');
		$secret  		= $this->__get('secret');
		$newpub  		= $this->__get('newpub');

		if (isset($selections[$this->_base]) && count($selections[$this->_base]) > 0)
		{
			$objPA = new PublicationAttachment( $this->_database );

			// Attach every selected file
			foreach ($selections[$this->_base] as $link)
			{

				if ($objPA->loadAttachment($vid, $link, 'link'))
				{
					$objPA->modified_by 			= $uid;
					$objPA->modified 				= JFactory::getDate()->toSql();
				}
				else
				{
					$objPA 							= new PublicationAttachment( $this->_database );
					$objPA->publication_id 			= $pid;
					$objPA->publication_version_id 	= $vid;
					$objPA->path 					= $link;
					$objPA->type 					= $this->_attachmentType;
					$objPA->created_by 				= $uid;
					$objPA->created 				= JFactory::getDate()->toSql();
					$objPA->title 					= NULL;
				}

				$objPA->ordering 					= $added;
				$objPA->role 						= $primary;
				$objPA->params 						= $primary  == 1 && $serveas ? 'serveas='.$serveas : $objPA->params;

				if ($objPA->store())
				{
					$added++;
				}
			}
		}

		return $added;
	}

	/**
	 * Cleanup publication attachments when others are picked
	 *
	 * @return     void
	 */
	protected function _cleanupAttachments()
	{
		// Incoming data
		$selections 	= $this->__get('selections');
		$vid  			= $this->__get('vid');
		$pid  			= $this->__get('pid');
		$uid  			= $this->__get('uid');
		$old  			= $this->__get('old');
		$secret  		= $this->__get('secret');

		if (empty($selections) || !isset($selections[$this->_base]))
		{
			return false;
		}

		if (!in_array(trim($old->path), $selections[$this->_base]))
		{
			$objPA = new PublicationAttachment( $this->_database );
			$objPA->deleteAttachment($vid, $old->path, $old->type);
		}

		return true;
	}
}
