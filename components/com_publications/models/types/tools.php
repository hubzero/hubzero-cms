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
 * TOOLS master type helper class
 */
class typeTools extends JObject
{
	/**
	 * JDatabase
	 *
	 * @var object
	 */
	var $_database       = NULL;

	/**
	 * Project
	 *
	 * @var object
	 */
	var $_project      	 = NULL;

	/**
	 * Base alias
	 *
	 * @var integer
	 */
	var $_base   		 = 'tools';

	/**
	 * Attachment type
	 *
	 * @var string
	 */
	var $_attachmentType = 'tool';

	/**
	 * Selection type (single/multi)
	 *
	 * @var boolean
	 */
	var $_multiSelect 	 = false;

	/**
	 * Allow change to selection after draft is started?
	 *
	 * @var boolean
	 */
	var $_changeAllowed  = false;

	/**
	 * Allow to create a new publication with exact same content?
	 *
	 * @var boolean
	 */
	var $_allowDuplicate  = false;

	/**
	 * Unique attachment properties
	 *
	 * @var array
	 */
	var $_attProperties  = array('object_name', 'object_instance');

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
	var $_serveas   	= 'invoke';

	/**
	 * Serve as choices
	 *
	 * @var string
	 */
	var $_serveChoices  = array('invoke');

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

			case 'checkContentStatus':
				$output = $this->_checkContentStatus();
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

		// Get tool
		$tool = new ProjectTool( $this->_database );
		if ($tool->loadTool($item))
		{
			$title = $tool->title;
		}

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

		if (!$attachments || count($attachments) < 0)
		{
			return false;
		}

		$toolname = $attachments[0]->object_name;

		// Get tool
		$objTool = new ProjectTool( $this->_database );
		$tool 	 = $objTool->getFullRecord($toolname);

		// Cannot publish if tool is not in working status
		$working = $tool && $tool->status_name == 'working' ? 1 : 2;

		return $working;
	}

	/**
	 * Check content
	 *
	 * @return     void
	 */
	protected function _checkContentStatus()
	{
		// Incoming data
		$selections	= $this->__get('selections');

		if (isset($selections['tools']) && !empty($selections['tools']))
		{
			$toolname = $selections['tools'][0];

			// Get tool
			$objTool = new ProjectTool( $this->_database );
			$tool 	 = $objTool->getFullRecord($toolname);

			// Cannot publish if tool is not in working status
			$working = $tool && $tool->status_name == 'working' ? 1 : 2;

			return $working;
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
		$item  = $this->__get('item');

		if (!$item)
		{
			return false;
		}

		$toolname = $item->object_name;
		$tool 	  = new ProjectTool($this->_database);
		if (!$tool->loadTool($toolname))
		{
			return true;
		}

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

		$toolname 	= $att->id ? $att->object_name : $item;
		$instanceId = $att->id ? $att->object_instance : NULL;
		$objT 	  	= new ProjectTool($this->_database);
		$tool 		= $objT->getFullRecord($toolname, NULL, $instanceId);
		if (!$tool->id)
		{
			return false;
		}

		$status = ' | ' . JText::_('PLG_PROJECTS_PUBLICATIONS_STATUS') . ': ';
		$status.= $tool->status_name != 'working'
				? '<span class="urgency">' . $tool->status_name . '</span>'
				: '<span class="green">' . $tool->status_name . '</span>';

		$title = $att->title ? $att->title : $tool->title;

		$html = '<span class="' . $this->_base . '">' . $title . '</span>';
		$html.= '<span class="c-iteminfo">' . $tool->name . ' ' . $status . '</span>';

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
		$primary  		= $this->__get('primary');
		$added  		= $this->__get('added');
		$serveas  		= $this->__get('serveas');
		$state  		= $this->__get('state');
		$secret  		= $this->__get('secret');
		$newpub  		= $this->__get('newpub');

		if (isset($selections['tools']) && count($selections['tools']) > 0)
		{
			$objPA = new PublicationAttachment( $this->_database );

			// Attach every selected tool
			foreach ($selections['tools'] as $toolname)
			{
				// Get tool
				$objTool = new ProjectTool( $this->_database );
				$tool = $objTool->getFullRecord($toolname);

				if (!$tool)
				{
					// Can't proceed
					continue;
				}

				if ($objPA->loadAttachment($vid, $toolname, 'tool'))
				{
					$objPA->modified_by 			= $uid;
					$objPA->modified 				= JFactory::getDate()->toSql();
				}
				else
				{
					$objPA = new PublicationAttachment( $this->_database );
					$objPA->publication_id 			= $pid;
					$objPA->publication_version_id 	= $vid;
					$objPA->path 					= '';
					$objPA->type 					= $this->_attachmentType;
					$objPA->created_by 				= $uid;
					$objPA->created 				= JFactory::getDate()->toSql();
				}

				// Save object information
				$objPA->object_id   	= $tool->id;
				$objPA->object_name 	= $tool->name;
				$objPA->object_revision = $tool->revision;
				$objPA->object_instance = $tool->instanceId;

				$objPA->ordering 		= $added;
				$objPA->role 			= $primary;
				$objPA->title 			= $tool->title;
				$objPA->params 			= $primary  == 1 && $serveas ? 'serveas='.$serveas : $objPA->params;

				if ($objPA->store())
				{
					$added++;
				}
			}
		}

		return $added;
	}
}
