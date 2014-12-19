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

// Include building blocks
include_once(dirname(__FILE__) . DS . 'blocks.php');
include_once(dirname(__FILE__) . DS . 'status.php');
include_once(dirname(__FILE__) . DS . 'attachments.php');
include_once(dirname(__FILE__) . DS . 'blockelements.php');
include_once(dirname(__FILE__) . DS . 'handlers.php');

// Include tables
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'curation.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'curation.history.php');
include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_publications' . DS . 'tables' . DS . 'block.php');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_publications'
	. DS . 'helpers' . DS . 'html.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'helper.php');

// Get language file
$lang = JFactory::getLanguage();
$lang->load('com_publications_curation');

/**
 * Publications curation class
 *
 * Parses curation flow into view block for user, admin and curator
 *
 */
class PublicationsCuration extends JObject
{
	/**
	* JDatabase
	*
	* @var object
	*/
	var $_db      		= NULL;

	/**
	* @var    object  Project
	*/
	var $_project      	= NULL;

	/**
	* @var    object  Publication
	*/
	var $_pub 			= NULL;

	/**
	* @var    string  Publication ID
	*/
	var $_pid 			= NULL;

	/**
	* @var    string  Publication version ID
	*/
	var $_vid 			= NULL;

	/**
	* @var    string Curation manifest
	*/
	var $_manifest 		= NULL;

	/**
	* @var    object Blocks
	*/
	var $_blocks 		= array();

	/**
	* @var    int total blocks
	*/
	var $_blockcount 	= 0;

	/**
	* @var    object Current block
	*/
	var $_block 		= array();

	/**
	* @var    string Current block name
	*/
	var $_blockname 	= NULL;

	/**
	* @var    string Current block sequence
	*/
	var $_blockorder 	= NULL;

	/**
	* @var    object
	*/
	var $_progress 		= NULL;

	/**
	* @var    string  Message
	*/
	var $_message 		= NULL;

	/**
	* Constructor
	*
	* @param      object  &$db      	 JDatabase
	* @param      string  $manifest     Pup type manifest
	* @return     void
	*/
	public function __construct( &$db, $manifest = NULL )
	{
		$this->_db 		 = $db;
		$this->_manifest = json_decode($manifest);

		// Parse blocks
		$this->setBlocks();
	}

	/**
	 * Get blocks in order
	 *
	 * @param   string  $manifest     Pup type manifest to parse
	 * @return  boolean
	 */
	public function setBlocks($manifest = NULL)
	{
		$manifest = $manifest ? $manifest : $this->_manifest;
		$blocks   = array();

		// We need a manifest
		if (!$manifest)
		{
			// Get blocks model
			$blocksModel = new PublicationsModelBlocks($this->_db);

			// Get default blocks
			$blocks = $blocksModel->getBlocks('block',
				" WHERE minimum=1 OR params LIKE '%default=1%'",
				" ORDER BY ordering, id"
			);

			$manifest = new stdClass;
			$manifest->blocks = new stdClass;
			$manifest->params = new stdClass;

			// Build default manifest
			if ($blocks && !empty($blocks))
			{
				$i = 1;
				foreach ($blocks as $blockname)
				{
					$blockManifest = $blocksModel->getManifest($blockname);

					if ($blockManifest)
					{
						$manifest->blocks->$i = $blockManifest;
						$i++;
					}
				}
			}

			$manifest->params->default_title 	= 'Untitled Draft';
			$manifest->params->default_category = 1;
			$manifest->params->require_doi 		= 1;
			$manifest->params->show_archival 	= 1;
			$this->_manifest 					= $manifest;
		}

		// Parse manifest (TBD)
		$this->_blocks = $manifest ? $manifest->blocks : NULL;

		// Get block count
		foreach ($this->_blocks as $b)
		{
			$this->_blockcount++;
		}

		return true;
	}

	/**
	 * Get active block
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @return  boolean
	 */
	public function setBlock($name = NULL, $sequence = 0)
	{
		if ($sequence && (!isset($this->_blocks->$sequence) || $this->_blocks->$sequence->name != $name))
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			return false;
		}

		$this->_block 		= $this->_blocks->$sequence;
		$this->_blockname 	= $this->_blocks->$sequence->name;
		$this->_blockorder 	= $sequence;
		return true;
	}

	/**
	 * Get block sequence
	 *
	 * @param   string  $name	Block name
	 * @return  integer
	 */
	public function getBlockSequence($name = NULL)
	{
		$sequence = NULL;
		$i = 1;
		foreach ($this->_blocks as $block)
		{
			if ($block->name == $name)
			{
				$sequence = $i;
				break;
			}
			$i++;
		}

		return $sequence;
	}

	/**
	 * Set association with publication and load curation
	 *
	 * @param   object  $pub	Publication
	 * @return  void
	 */
	public function setPubAssoc($pub = NULL)
	{
		$this->_pid 	= is_object($pub) ? $pub->id : NULL;
		$this->_vid 	= is_object($pub) ? $pub->version_id : NULL;
		$this->_pub		= $pub;

		// Set progress
		$this->setProgress();
	}

	/**
	 * Get schema for metadata elements
	 *
	 * @return  array
	 */
	public function getMetaSchema()
	{
		if (!$this->_blocks)
		{
			return false;
		}

		$customFields = array();

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		// Find all blocks of the same parent
		foreach ($this->_blocks as $sequence => $block)
		{
			$parentBlock = $blocksModel->getBlockProperty($block->name, '_parentname');

			if ($parentBlock == 'description')
			{
				foreach ($block->elements as $elId => $element)
				{
					if ($element->params->field != 'metadata')
					{
						continue;
					}

					$customFields['fields'][] = array(
						'default' 	=> '',
						'name' 		=> $element->params->aliasmap,
						'label' 	=> $element->label,
						'type'		=> $element->params->input,
						'required'	=> $element->params->required
					);
				}
			}
		}

		return json_encode($customFields);
	}

	/**
	 * Get manifests elements of interest
	 *
	 * @param   integer  $role		Element role
	 * @param   object   $handler	Handler
	 * @return  array
	 */
	public function getElements( $role = 1, $handler = NULL )
	{
		if (!$this->_blocks)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		$elements = array();

		// Find all blocks of the same parent
		foreach ($this->_blocks as $sequence => $block)
		{
			$parentBlock = $blocksModel->getBlockProperty($block->name, '_parentname');

			if ($parentBlock == 'content')
			{
				foreach ($block->elements as $elId => $element)
				{
					if ($element->params->type != 'file')
					{
						// continue;
					}

					if ($element->params->role == $role)
					{
						$output 			= new stdClass;
						$output->block 		= $block->params;
						$output->sequence 	= $sequence;
						$output->id 		= $elId;
						$output->manifest 	= $element;
						$elements[] = $output;
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Get manifest for element of block type (content OR description)
	 *
	 * @param   integer  $elementId		Element ID
	 * @param   string   $name			Block name
	 * @return  mixed: object or boolean False
	 */
	public function getElementManifest( $elementId = 0, $name = 'content')
	{
		if (!$elementId)
		{
			return false;
		}

		if (!$this->_blocks)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		// Find all blocks of the same parent
		foreach ($this->_blocks as $sequence => $block)
		{
			$parentBlock = $blocksModel->getBlockProperty($block->name, '_parentname');

			// Go through elements
			if ($parentBlock == $name)
			{
				foreach ($block->elements as $elId => $element)
				{
					if ($elId == $elementId)
					{
						$output = new stdClass;
						$output->block 		= $block;
						$output->sequence 	= $sequence;
						$output->element 	= $element;
						return $output;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Parse block
	 *
	 * @param   string  $name		Who is viewing block content?
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @return  string HTML
	 */
	public function parseBlock( $viewer = 'edit', $name = NULL, $sequence = 0 )
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;

		// Set the block
		if ($name)
		{
			if (!$sequence)
			{
				$sequence = $this->getBlockSequence($name);
			}

			if (!$sequence)
			{
				$this->setError( JText::_('Error loading block') );
				return false;
			}

			$this->_block 		= $this->_blocks->$sequence;
			$this->_blockname 	= $this->_blocks->$sequence->name;
			$this->_blockorder 	= $sequence;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		return $blocksModel->renderBlock($this->_blockname, $viewer, $this->_block, $this->_pub, $sequence);
	}

	/**
	 * Reorder attached items
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function reorder ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		$blocksModel->reorder($this->_blockname, $this->_block, $this->_blockorder, $this->_pub, $actor, $elementId);

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		// Set success message
		if ($blocksModel->get('_message'))
		{
			$this->set('_message', $blocksModel->get('_message'));
		}

		// Record update requested?
		if ($blocksModel->get('_update'))
		{
			// Record update time
			$data 				= new stdClass;
			$data->updated 		= JFactory::getDate()->toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Save attached item info
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function saveItem ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		$blocksModel->saveItem($this->_blockname, $this->_block, $this->_blockorder, $this->_pub, $actor, $elementId);

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		// Set success message
		if ($blocksModel->get('_message'))
		{
			$this->set('_message', $blocksModel->get('_message'));
		}

		// Record update requested?
		if ($blocksModel->get('_update'))
		{
			// Record update time
			$data 				= new stdClass;
			$data->updated 		= JFactory::getDate()->toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Save attached item info
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function deleteItem ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		$blocksModel->deleteItem($this->_blockname, $this->_block, $this->_blockorder, $this->_pub, $actor, $elementId);

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		// Set success message
		if ($blocksModel->get('_message'))
		{
			$this->set('_message', $blocksModel->get('_message'));
		}

		// Record update requested?
		if ($blocksModel->get('_update'))
		{
			// Record update time
			$data 				= new stdClass;
			$data->updated 		= JFactory::getDate()->toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Dispute request for change
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function dispute ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Incoming
		$dispute  = urldecode(JRequest::getVar('review', ''));

		if (!trim($dispute))
		{
			$this->setError('Please provide a reason for dispute');
			return false;
		}

		// Record update time
		$data 				= new stdClass;
		$data->updated 		= JFactory::getDate()->toSql();
		$data->updated_by 	= $actor;
		$data->update		= stripslashes($dispute);
		$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);

		$this->set('_message', 'Curator change request disputed');

		return true;
	}

	/**
	 * Skip requirement
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function skip ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Incoming
		$reason  = urldecode(JRequest::getVar('review', ''));

		if (!trim($reason))
		{
			$this->setError('Please provide a reason for skipping requirement');
			return false;
		}

		// Record update time
		$data 				= new stdClass;
		$data->updated 		= JFactory::getDate()->toSql();
		$data->updated_by 	= $actor;
		$data->review_status = 3;
		$data->update		= stripslashes($reason);
		$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);

		$this->set('_message', 'Your request has been saved');

		return true;
	}

	/**
	 * Remove dispute
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function undispute ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Delete message
		$data 				= new stdClass;
		$data->update		= NULL;
		$data->updated 		= NULL;
		$data->updated_by 	= 0;
		$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);

		$this->set('_message', 'Dispute cleared. Please make changes requested by curator.');

		return true;
	}

	/**
	 * Attach new record
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function addItem ($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		$blocksModel->addItem($this->_blockname, $this->_block, $this->_blockorder, $this->_pub, $actor, $elementId);

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		// Set success message
		if ($blocksModel->get('_message'))
		{
			$this->set('_message', $blocksModel->get('_message'));
		}

		// Record update requested?
		if ($blocksModel->get('_update'))
		{
			// Record update time
			$data 				= new stdClass;
			$data->updated 		= JFactory::getDate()->toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Save new block information
	 *
	 * @param   integer  $actor			Actor user ID
	 * @param   integer  $elementId		Element ID
	 * @return  boolean
	 */
	public function saveBlock($actor = 0, $elementId = 0)
	{
		if (!$this->_blocks || !$this->_block || !$this->_pub)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		// Save data
		$blocksModel->saveBlock($this->_blockname, $this->_block, $this->_blockorder, $this->_pub, $actor, $elementId);

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		// Set success message
		if ($blocksModel->get('_message'))
		{
			$this->set('_message', $blocksModel->get('_message'));
		}

		// Record update requested?
		if ($blocksModel->get('_update'))
		{
			// Record update time
			$data 				= new stdClass;
			$data->updated 		= JFactory::getDate()->toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Draw publication draft status bar
	 *
	 * @return  string HTML
	 */
	public function drawStatusBar()
	{
		if (!$this->_progress)
		{
			return false;
		}

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'draft',
				'layout'	=>'statusbar'
			)
		);
		$view->pub 			 = $this->_pub;
		$view->progress		 = $this->_progress;
		$view->active		 = $this->_blockname;
		$view->activenum	 = $this->_blockorder;
		$view->database		 = $this->_db;
		$view->display();
	}

	/**
	 * Check if block is in manifest
	 *
	 * @param   string  $name	Block name
	 * @return  boolean
	 */
	public function blockExists( $name = NULL )
	{
		if (!$this->_blocks || $name === NULL)
		{
			return false;
		}

		// Check status for each
		foreach ($this->_blocks as $sequence => $block)
		{
			if ($block->name == $name)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Set curation progress for publication
	 *
	 * @return  void
	 */
	public function setProgress()
	{
		$result = new stdClass;

		if (!$this->_blocks)
		{
			return false;
		}

		$result->lastBlock 	= 0;
		$result->firstBlock = 0;
		$result->blocks = new stdClass();

		$i = 0;
		$k = 0;

		// Check status for each
		foreach ($this->_blocks as $sequence => $block)
		{
			// Skip inactive blocks
			if (isset($block->active) && $block->active == 0)
			{
				continue;
			}
			$autoStatus 		= self::getStatus($block->name, $this->_pub, $sequence);
			$reviewStatus		= self::getReviewStatus($block->name, $this->_pub, $sequence);

			$result->blocks->$sequence = new stdClass();
			$result->blocks->$sequence->name 		= $block->name;
			$result->blocks->$sequence->manifest 	= $block;
			$result->blocks->$sequence->firstElement= self::getFirstElement($block->name, $this->_pub, $sequence);

			if ($autoStatus->status > 0)
			{
				$result->lastBlock = $sequence;
			}

			if (!$result->firstBlock)
			{
				if (($reviewStatus && $reviewStatus->status == 0
					&& !$reviewStatus->lastupdate) || $autoStatus->status == 0)
				{
					$result->firstBlock = $sequence;
				}
				elseif (!$reviewStatus && $autoStatus->status == 0)
				{
					$result->firstBlock = $sequence;
				}
			}

			$k++;

			if (($autoStatus->status > 0 && $reviewStatus->status != 0) || $reviewStatus->status == 1 || $reviewStatus->lastupdate)
			{
				$i++;
			}

			// Look at both auto and review status to determine if complete
			if ($reviewStatus)
			{
				foreach ($block->elements as $elementId => $element)
				{
					if ($autoStatus->elements->$elementId->status == 0 && $reviewStatus->elements->$elementId->status == 2)
					{
						$i--;
						$reviewStatus->status = 2;
					}
				}
			}
			$result->blocks->$sequence->status 		= $autoStatus;
			$result->blocks->$sequence->review      = $reviewStatus;
		}

		// Are all sections complete for submission?
		$result->complete  = $i == $k ? 1 : 0;

		$this->_progress = $result;
	}

	/**
	 * Transfer content from one version to another
	 *
	 * @param   object  $pub	Publication object
	 * @param   object  $old	Transfer from version record
	 * @param   object  $new	Transfer to version record
	 * @return  boolean
	 */
	public function transfer( $pub, $old, $new)
	{
		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);

		foreach ($pub->_curationModel->_progress->blocks as $sequence => $block)
		{
			$parentBlock = $blocksModel->getBlockProperty($block->name, '_parentname');

			if (in_array($parentBlock, array('content', 'authors')))
			{
				$blocksModel->transferData($parentBlock, $block->manifest, $pub, $old, $new);
			}
		}

		// Set error
		if ($blocksModel->getError())
		{
			$this->setError($blocksModel->getError());
		}

		return true;
	}

	/**
	 * Check block status (auto check)
	 *
	 * @param   string  $name		Block name
	 * @param   object  $pub		Publication object
	 * @param   integer $sequence	Block order in curation
	 * @return  object
	 */
	public function getStatus( $name, $pub, $sequence = 0)
	{
		$pub = $pub ? $pub : $this->_pub;

		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return false;
		}

		// Get blocks model
		$blocksModel = new PublicationsModelBlocks($this->_db);
		return $blocksModel->getStatus($name, $pub, $this->_blocks->$sequence);

		// Return status
		return $status;
	}

	/**
	 * Get first element ID
	 *
	 * @param   string  $name		Block name
	 * @param   object  $pub		Publication object
	 * @param   integer $sequence	Block order in curation
	 * @return  integer
	 */
	public function getFirstElement( $name, $pub, $sequence = 0)
	{
		$pub = $pub ? $pub : $this->_pub;
		$elementId = 0;

		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $elementId;
		}

		if ($this->_blocks->$sequence->elements)
		{
			foreach ($this->_blocks->$sequence->elements as $id => $element)
			{
				return $id;
			}
		}

		// Return status
		return $elementId;
	}

	/**
	 * Get first block ID
	 *
	 * @return  integer
	 */
	public function getFirstBlock()
	{
		foreach ($this->_blocks as $id => $block)
		{
			return $id;
		}
	}

	/**
	 * Get next block ID
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @return  integer
	 */
	public function getNextBlock( $name, $sequence = 0, $activeId = 1)
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		foreach ($this->_blocks as $id => $block)
		{
			if (isset($block->active) && $block->active == 0)
			{
				continue;
			}
			if ($id == $sequence)
			{
				$start = 1;
			}
			if ($start == 1 && $id != $sequence)
			{
				$remaining[] = $id;
			}
		}

		// Return element ID
		return empty($remaining) ? $sequence : $remaining[0];
	}

	/**
	 * Determine if block is coming
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @param   integer $activeId	Active block ID
	 * @param   integer $elementId	Element ID in question
	 * @return  boolean
	 */
	public function isBlockComing( $name, $sequence = 0, $activeId = 1)
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		foreach ($this->_blocks as $id => $block)
		{
			if (isset($block->active) && $block->active == 0)
			{
				continue;
			}
			if ($id == $activeId )
			{
				$start = 1;
			}
			if ($start == 1 && $id != $activeId)
			{
				$remaining[] = $id;
			}
		}

		return in_array($sequence, $remaining) ? true : false;
	}

	/**
	 * Get previous block ID
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @return  integer
	 */
	public function getPreviousBlock( $name, $sequence = 0, $activeId = 1)
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		foreach ($this->_blocks as $id => $block)
		{
			if (isset($block->active) && $block->active == 0)
			{
				continue;
			}
			if ($id == $sequence)
			{
				$start = 1;
			}
			if ($start == 0 && $id != $sequence)
			{
				$remaining[] = $id;
			}
		}

		// Return element ID
		return empty($remaining) ? $sequence : end($remaining);
	}

	/**
	 * Get next element ID
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @param   integer $activeId	Active element ID
	 * @return  integer
	 */
	public function getNextElement( $name, $sequence = 0, $activeId = 1)
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		if ($this->_blocks->$sequence->elements)
		{
			foreach ($this->_blocks->$sequence->elements as $id => $element)
			{
				if ($id == $activeId)
				{
					$start = 1;
				}
				if ($start == 1 && $id != $activeId)
				{
					$remaining[] = $id;
				}
			}
		}

		// Return element ID
		return empty($remaining) ? $activeId : $remaining[0];
	}

	/**
	 * Determine if element is coming
	 *
	 * @param   string  $name		Block name
	 * @param   integer $sequence	Block order in curation
	 * @param   integer $activeId	Active element ID
	 * @param   integer $elementId	Element ID in question
	 * @return  boolean
	 */
	public function isComing( $name, $sequence = 0, $activeId = 1, $elementId = 0)
	{
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		if ($this->_blocks->$sequence->elements)
		{
			foreach ($this->_blocks->$sequence->elements as $id => $element)
			{
				if ($id == $activeId)
				{
					$start = 1;
				}
				if ($start == 1 && $id != $activeId)
				{
					$remaining[] = $id;
				}
			}
		}

		return in_array($elementId, $remaining) ? true : false;
	}

	/**
	 * Check block element status (auto check)
	 *
	 * @param   string  $name		Block name
	 * @param   integer $elementId	Element ID in question
	 * @param   object  $pub		Publication object
	 * @param   integer $sequence	Block order in curation
	 * @return  object
	 */
	public function getElementStatus( $name, $elementId = NULL, $pub, $sequence = 0)
	{
		$pub = $pub ? $pub : $this->_pub;

		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$sequence)
		{
			$this->setError( JText::_('Error loading block') );
			return false;
		}

		// Get blocks model
		$blocksModel 	= new PublicationsModelBlocks($this->_db);
		return $blocksModel->getStatus($name, $pub, $this->_blocks->$sequence, $elementId );
	}

	/**
	 * Save version label
	 *
	 * @param      int $uid
	 * @return     boolean
	 */
	public function saveVersionLabel( $uid = 0 )
	{
		if (!$this->_pub)
		{
			return false;
		}

		$row = new PublicationVersion( $this->_db );

		// Incoming
		$label = trim(JRequest::getVar( 'label', '', 'post' ));
		$used_labels = $row->getUsedLabels( $this->_pub->id, $this->_pub->version );

		if ($label && in_array($label, $used_labels))
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );
			return false;
		}
		elseif ($label)
		{
			if (!$row->loadVersion($this->_pub->id, $this->_pub->version))
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_ERROR') );
				return false;
			}

			$row->version_label = $label;
			if (!$row->store())
			{
				$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_ERROR') );
			}
		}

		// Success message
		$this->set('_message', JText::_('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL_SAVED'));

		return true;
	}

	/**
	 * Check status for curation review
	 *
	 * @param   string  $block		Block name
	 * @param   object  $pub		Publication object
	 * @param   integer $sequence	Block order in curation
	 * @return  object
	 */
	public function getReviewStatus( $block, $pub, $sequence = 0)
	{
		// Get status model
		$status = new PublicationsModelStatus();

		if (!isset($pub->reviewedItems))
		{
			$pub->reviewedItems = $pub->_curationModel->getReviewedItems($pub->version_id);
		}

		$manifest = $this->_blocks->$sequence;

		// Get element status
		if ($manifest->elements)
		{
			$i 		 	= 0;
			$success 	= 0;
			$failed 	= 0;
			$incomplete = 0;
			$pending 	= 0;
			$skipped	= 0;

			foreach ($manifest->elements as $elementId => $element)
			{
				$props = $block . '-' . $sequence . '-' . $elementId;
				if (!isset($status->elements))
				{
					$status->elements = new stdClass();
				}
				$status->elements->$elementId = $this->getReviewItemStatus( $props, $pub->reviewedItems);

				// Store element label (for history tracking)
				$status->elements->$elementId->label = $element->label;

				if ($status->elements->$elementId->status >= 1
					|| $status->elements->$elementId->lastupdate)
				{
					$success++;
				}
				if ($status->elements->$elementId->status == 0 && !$status->elements->$elementId->lastupdate)
				{
					$failed++;
				}
				if ($status->elements->$elementId->status == 2)
				{
					$incomplete++;
				}
				if ($status->elements->$elementId->status == 0 && $status->elements->$elementId->lastupdate)
				{
					$pending++;
				}
				if ($status->elements->$elementId->status == 3)
				{
					$skipped++;
				}
				$i++;
			}

			// Determine block status based on element status
			$passed 	    	= ($success == $i || $pub->state == 1) ? 1 : 0;
			$status->status 	= $failed > 0 ? 0 : $passed;
			$status->status 	= $incomplete == $i ? 2 : $status->status; // unreviewed
			$status->lastupdate = ($pending > 0 || $skipped > 0) && $passed == 1 ? true : NULL;
		}
		else
		{
			$props = $block . '-' . $sequence;
			return $this->getReviewItemStatus( $props, $pub->reviewedItems);
		}

		// Return status
		return $status;
	}

	/**
	 * Get status of curation item
	 *
	 * @param   string  $props		Pointer to block/element in question
	 * @param   array   $items		Status array
	 * @return  object
	 */
	public function getReviewItemStatus( $props = NULL, $items = NULL )
	{
		$status = new PublicationsModelStatus();
		$status->status 		= 2; // unreviewed
		$status->updated_by 	= 0;

		if ($props === NULL || $items === NULL)
		{
			return $status;
		}

		$record = isset($items[$props]) ? $items[$props] : NULL;

		if (!$record)
		{
			return $status;
		}

		if ($record->review_status == 1)
		{
			$status->status = 1;
		}
		elseif ($record->review_status == 3)
		{
			$status->status = 3;
		}
		elseif ($record->review_status == 2)
		{
			$status->status  = 0;
			$status->setError($record->review);
		}

		// Was item updated by authors?
		if ($record->reviewed && $record->updated > $record->reviewed)
		{
			$status->lastupdate = $record->updated;
			$status->updated_by = $record->updated_by;
		}
	//	if ($record->update && $record->updated > $record->reviewed)
	//	{
			$status->message = $record->update;
	//	}

		return $status;
	}

	/**
	 * Parse curation status for display
	 *
	 * @param   object  $pub	Publication object
	 * @param   integer $step	Block order in curation
	 * @param   integer $elId	Element ID in question
	 * @param   string  $viewer	Author or curator
	 * @return  object
	 */
	public function getCurationStatus( $pub, $step, $elId = 0, $viewer = 'author' )
	{
		$status 		 		= new stdClass;
		$status->updated 		= NULL;
		$status->curatornotice  = NULL;
		$status->status  		= 2;
		$status->updatenotice 	= NULL;
		$status->authornotice 	= NULL;
		$status->updated_by		= 0;

		if ($elId)
		{
			$reviewStatus = $pub->_curationModel->_progress->blocks->$step->review->elements->$elId;
		}
		else
		{
			$reviewStatus = $pub->_curationModel->_progress->blocks->$step->review;
		}

		if (!$reviewStatus)
		{
			return $status;
		}

		$status->status 		= $reviewStatus->status;
		$status->curatornotice 	= $reviewStatus->getError();
		$status->updated		= $pub->state != 1 ? $reviewStatus->lastupdate : NULL;
		$status->authornotice 	= $reviewStatus->message;

		if ($status->updated && isset($reviewStatus->updated_by) && $reviewStatus->updated_by)
		{
			$profile = \Hubzero\User\Profile::getInstance($reviewStatus->updated_by);
			$by 	 = ' ' . JText::_('COM_PUBLICATIONS_CURATION_BY') . ' ' . $profile->get('name');

			if ($status->status != 3)
			{
				$status->updatenotice 	= JText::_('COM_PUBLICATIONS_CURATION_UPDATED') . ' '
					. JHTML::_('date', $status->updated, 'M d, Y H:i') . $by;
			}
			else
			{
				$status->updatenotice 	= JText::_('COM_PUBLICATIONS_CURATION_SKIPPED') . ' ' . $by;
			}
		}

		return $status;
	}

	/**
	 * Show curator notice
	 *
	 * @param   object  $curatorStatus	Status object
	 * @param   string  $props			Pointer to block/element in question
	 * @param   string  $viewer			Author or curator
	 * @param   string  $elName			Element name
	 * @return  void
	 */
	public function drawCurationNotice( $curatorStatus, $props, $viewer = 'author', $elName = '' )
	{
		?>
		<?php if ($viewer == 'curator' && !$curatorStatus->updatenotice) { ?>
		<span class="edit-notice">[<a href="#">edit</a>]</span>
		<?php } ?>
		<?php if (($viewer == 'author' && (!$curatorStatus->curatornotice && $curatorStatus->status == 3)) || ($viewer == 'curator' && !$curatorStatus->updatenotice && !$curatorStatus->curatornotice)) { return; }?>
		<div class="status-notice">
			<span class="update-notice"><?php if ($viewer == 'curator') { echo  $curatorStatus->updatenotice; }
			elseif ($curatorStatus->status != 3) {
				if ($curatorStatus->authornotice && $curatorStatus->updated)
				{
					?>
						<span class="dispute-notice">
							<span class="remove-notice" id="<?php echo $props; ?>">[<a href="#<?php echo $elName; ?>"><?php echo JText::_('COM_PUBLICATIONS_CURATION_DISPUTE_DELETE'); ?></a>]</span>
							<?php echo JText::_('COM_PUBLICATIONS_CURATION_DISPUTE_NOTICE'); ?>
							<span class="dispute-text"><?php echo $curatorStatus->authornotice; ?></span>
						</span>
				<?php }
				else
				{
					echo  JText::_('COM_PUBLICATIONS_CURATION_NOTICE_UPDATED');
				}
			} ?></span>
			<?php if ($viewer == 'author' && $curatorStatus->curatornotice && !$curatorStatus->updated) {  ?>
			<span class="disputeit" id="<?php echo $props; ?>">[<a href="#<?php echo $elName; ?>"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_THIS'); ?></a>]</span>
			<?php } ?>

			<span class="fail-notice"><?php echo $viewer == 'curator' ? JText::_('COM_PUBLICATIONS_CURATION_NOTICE_TO_AUTHORS') : JText::_('COM_PUBLICATIONS_CURATION_CHANGE_REQUEST'); ?></span>
			<span class="notice-text"><?php echo $curatorStatus->curatornotice; ?></span>
			<?php if ($curatorStatus->authornotice && $viewer == 'curator') { ?>
			<span class="dispute-notice">
				<strong><?php echo JText::_('COM_PUBLICATIONS_CURATION_DISPUTE_NOTICE'); ?></strong>
				<?php echo $curatorStatus->authornotice; ?>
			</span>
			<?php } ?>
		</div>
	<?php }

	/**
	 * Draw curation checker
	 *
	 * @param   string  $props			Pointer to block/element in question
	 * @param   object  $reviewStatus	Status object
	 * @param   string  $url			Action URL
	 * @param   string  $title
	 * @return  void
	 */
	public function drawChecker( $props, $reviewStatus, $url, $title = '' )
	{
		$status  = $reviewStatus->status;
		$updated = $reviewStatus->updated;
		?>
		<div class="block-checker" id="<?php echo $props; ?>" rel="<?php echo $title; ?>">
			<span class="checker-pass <?php echo ($status == 1) ? 'picked' : ''; ?><?php echo $updated ? ' updated' : ''; ?>"><a href="<?php echo $url; ?>" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_APPROVE'); ?>"></a></span>
			<span class="checker-fail <?php echo $status == 0 ? 'picked' : ''; ?><?php echo $updated ? ' updated' : ''; ?>"><a href="#addnotice" title="<?php echo JText::_('COM_PUBLICATIONS_CURATION_NOT_APPROVE'); ?>"></a></span>
		</div>
	<?php
	}

	/**
	 * Get curation reviews for version ID
	 *
	 * @param   integer  $versionId
	 * @return  array or boolean False
	 */
	public function getReviewedItems( $versionId = 0 )
	{
		if (!$versionId)
		{
			return false;
		}

		$review = array();

		$curation = new PublicationCuration($this->_db);
		$results = $curation->getRecords($versionId);

		if ($results)
		{
			foreach ($results as $result)
			{
				$prop = $result->block . '-' . $result->step;
				$prop.= $result->element ? '-' . $result->element : '';
				$review[$prop] = $result;
			}
		}
		else
		{
			return false;
		}

		return $review;
	}

	/**
	 * Get change log
	 *
	 * @param   object  $pub		Publication object
	 * @param   integer $oldStatus	Previous version state
	 * @param   integer $newStatus	New version state
	 * @param   integer $curator	Author or curator
	 * @return  string
	 */
	public function getChangeLog( $pub, $oldStatus = 0, $newStatus = 0, $curator = 0 )
	{
		$changelog  = NULL;

		switch ($newStatus)
		{
			case 7:
				// Kicked back
				$changelog .= 'reviewed and kicked back to authors';
			break;

			case 5:
				// Submitted
				$changelog .= $oldStatus == 7
				? 'updated and re-submitted for review' : ' submitted for review';
			break;

			case 1:
				// Submitted
				$changelog .= 'approved and published';
			break;

			case 4:
				// Saved or reverted
				$changelog .= $oldStatus == 1
				? 'reverted to draft' : 'saved draft for internal review';
			break;
		}

		// Add details
		if ($pub->_curationModel->_progress && ($newStatus == 7 || $oldStatus == 7))
		{
			$changelog .= '<hr />';
			$changelog .= $newStatus == 7
						? '<p>Changes requested for sections: </p>'
						: '<p>Updated sections include: </p>';
			$changelog .= '<ul>';
			foreach ($this->_progress->blocks as $sequence => $block)
			{
				if ($block->review && (($newStatus == 7 && $block->review->status == 0)
					|| ($oldStatus == 7 && $block->review->lastupdate)))
				{
					$changelog .= '<li>';
					$changelog .= $block->manifest->label;
					if ($block->review->elements)
					{
						foreach ($block->review->elements as $element)
						{
							if ($element->getError() || $element->message)
							{
								$changelog .= '<span class="prominent">' . $element->label . '</span>';
							}
							if ($element->getError())
							{
								$changelog .= '<span class="italic">Change request:</span>';
								$changelog .= '<span>' . $element->getError() . '</span>';
							}
							if ($element->message)
							{
								$changelog .= '<span class="italic">Author response:</span>';
								$changelog .= '<span>' . $element->message . '</span>';
							}
						}
					}
					if ($block->review->getError())
					{
						$changelog .= '<span class="italic">Change request:</span>';
						$changelog .= '<span>' . $block->review->getError() . '</span>';
					}
					if ($block->review->message)
					{
						$changelog .= '<span class="italic">Author response:</span>';
						$changelog .= '<span>' . $block->review->message . '</span>';
					}
					$changelog .= '</li>';
				}
			}
			$changelog .= '</ul>';
		}

		return $changelog;
	}

	/**
	 * Save history log
	 *
	 * @param   object  $pub		Publication object
	 * @param   integer $actor		Actor user ID
	 * @param   integer $oldStatus	Previous version state
	 * @param   integer $newStatus	New version state
	 * @param   integer $curator	Author or curator
	 * @return  boolean
	 */
	public function saveHistory( $pub, $actor = 0, $oldStatus = 0, $newStatus = 0, $curator = 0 )
	{
		// Incoming
		$comment = JRequest::getVar('comment', '', 'post');

		// Collect details
		$changelog = $this->getChangeLog($pub, $oldStatus, $newStatus, $curator);

		if (!$changelog)
		{
			return false;
		}

		$obj = new PublicationCurationHistory($this->_db);

		// Create new record
		$obj->publication_version_id 	= $pub->version_id;
		$obj->created 					= JFactory::getDate()->toSql();
		$obj->created_by				= $actor;
		$obj->changelog					= $changelog;
		$obj->curator					= $curator;
		$obj->newstatus					= $newStatus;
		$obj->oldstatus					= $oldStatus;
		$obj->comment					= \Hubzero\Utility\Sanitize::clean(htmlspecialchars($comment));

		if ($obj->store())
		{
			return true;
		}

		return false;
	}

	/**
	 * Get history logs
	 *
	 * @param   object  $pub		Publication object
	 * @param   integer $curator	Author or curator
	 * @return  object or NULL
	 */
	public function getHistory( $pub, $curator = 0 )
	{
		$obj = new PublicationCurationHistory($this->_db);

		$history = $obj->getRecords($pub->version_id);

		return $history;
	}

	/**
	 * Get last
	 *
	 * @param   integer  $elementId		Element ID
	 * @param   string   $name			Block name
	 * @param   object   $pub			Publication object
	 * @param   integer  $sequence		Block order in curation
	 * @return boolean
	 */
	public function getLastUpdate( $elementId, $name, $pub, $sequence )
	{
		$curation = new PublicationCuration($this->_db);
		return $curation->getRecord($pub->id, $pub->version_id, $name, $sequence, $elementId);
	}

	/**
	 * Save update
	 *
	 * @param   object   $data			Data to save
	 * @param   integer  $elementId		Element ID
	 * @param   string   $name			Block name
	 * @param   object   $pub			Publication object
	 * @param   integer  $sequence		Block order in curation
	 * @return boolean
	 */
	public function saveUpdate( $data = NULL, $elementId, $name, $pub, $sequence )
	{
		if ($data === NULL)
		{
			return false;
		}

		$name 	  = $name ? $name : $this->_blockname;
		$pub 	  = $pub ? $pub : $this->_pub;
		$sequence = $sequence ? $sequence : $this->_blockorder;
		if (!$sequence)
		{
			$sequence = $this->getBlockSequence($name);
		}

		if (!$pub || !$name || !$sequence)
		{
			return false;
		}

		$curation = new PublicationCuration($this->_db);

		// Load curation record if exists
		if ($curation->loadRecord($pub->id, $pub->version_id, $name, $sequence, $elementId))
		{
			// Record found - update
		}
		else
		{
			// Create new record
			$curation->publication_id 			= $pub->id;
			$curation->publication_version_id 	= $pub->version_id;
			$curation->block 					= $name;
			$curation->step						= $sequence;
			$curation->element					= $elementId;
		}

		// Insert incoming data
		foreach ($data as $field => $value)
		{
			$field = trim($field);
			$curation->$field = trim($value);
		}

		if ($curation->store())
		{
			return true;
		}

		return false;
	}

	/**
	 * Produce publication package
	 *
	 *
	 * @return     boolean
	 */
	public function showPackageContents()
	{
		if (!$this->_pub)
		{
			return false;
		}

		// Get elements in primary and supporting role
		$prime    = $this->getElements(1);
		$second   = $this->getElements(2);
		$elements = array_merge($prime, $second);

		// Do we have items to package?
		if (!$elements)
		{
			return '<p class="witherror">' . JText::_('COM_PUBLICATIONS_CURATION_PACKAGE_ERROR_NO_FILES') . '</p>';
		}

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_db);
		$contents = '<ul class="filelist">';

		$contents .= $attModel->showPackagedItems(
			$elements,
			$this->_pub
		);

		$txt   = '<img src="' . ProjectsHtml::getFileIcon('txt') . '" alt="txt" />';

		// Custom license to be included in LICENSE.txt
		if ($this->_pub->license_text)
		{
			$contents .= '<li>' . $txt . ' LICENSE.txt</li>';
		}
		$contents .= '<li>' . $txt . ' README.txt</li>';
		$contents .= '</ul>';

		return $contents;
	}

	/**
	 * Produce publication package
	 *
	 *
	 * @return     boolean
	 */
	public function package()
	{
		if (!$this->_pub)
		{
			return false;
		}

		// Get elements in primary and supporting role
		$prime    = $this->getElements(1);
		$second   = $this->getElements(2);
		$elements = array_merge($prime, $second);

		// Do we have items to package?
		if (!$elements)
		{
			return false;
		}

		// Get publications helper
		$helper = new PublicationHelper($this->_db, $this->_pub->version_id, $this->_pub->id);

		// Get publication path
		$pubBase = $helper->buildPath($this->_pub->id, $this->_pub->version_id, '', '', 1);

		// Empty draft?
		if (!file_exists($pubBase))
		{
			return false;
		}

		// Set archival properties
		$bundleDir  = $this->_pub->title;
		$tarname 	= JText::_('Publication') . '_' . $this->_pub->id . '.zip';
		$tarpath 	= $pubBase . DS . $tarname;
		$licFile 	= $pubBase . DS . 'LICENSE.txt';
		$readmeFile = $pubBase . DS . 'README.txt';

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_db);

		// Start README
		$readme  = $this->_pub->title . "\n ";
		$readme .= 'Version ' . $this->_pub->version_label . "\n ";

		// List authors
		if (isset($this->_pub->_authors) && $this->_pub->_authors)
		{
			$readme .= 'Authors: ' . "\n ";

			foreach ($this->_pub->_authors as $author)
			{
				$readme .= ($author->name) ? $author->name : $author->p_name;
				$org = ($author->organization) ? $author->organization : $author->p_organization;

				if ($org)
				{
					$readme .= ', ' . $org;
				}
				$readme .= "\n ";
			}
		}

		// Add DOI if available
		if ($this->_pub->doi)
		{
			$readme .= 'doi:' . $this->_pub->doi . "\n ";
		}

		// Add license information
		$objL = new PublicationLicense( $this->_db );
		if ($objL->loadLicense($this->_pub->license_type) && $objL->id)
		{
			$readme .= "\n " . "\n ";
			$readme .= 'License: ' . "\n ";
			$readme .= $objL->title . "\n ";

			// Custom license text?
			if ($this->_pub->license_text)
			{
				$readme .= $this->_pub->license_text . "\n ";

				// Create license file
				$handle  = fopen($licFile, 'w');
				fwrite($handle, $this->_pub->license_text);
				fclose($handle);
			}
			elseif ($objL->text)
			{
				$readme .= $objL->text . "\n ";
			}
		}

		$readme .= "\n ";
		$readme .= '#####################################' . "\n ";
		$readme .= 'Included Publication Materials:' . "\n ";
		$readme .= '#####################################' . "\n ";

		// Create bundle
		$zip = new ZipArchive;
		if ($zip->open($tarpath, ZipArchive::OVERWRITE) === TRUE)
		{
			// Bundle file attachments
			$attModel->bundleItems(
				$zip,
				$elements,
				$this->_pub,
				$readme,
				$bundleDir
			);

			// Add license file
			if (file_exists($licFile))
			{
				$where = $bundleDir . DS . basename($licFile);
				$zip->addFile($licFile, $where);
				$readme   .= "\n" . 'License File: ' . "\n";
				$readme   .= '>>> ' . basename($licFile) . "\n";
			}

			// Add readme
			if ($readme)
			{
				$where = $bundleDir . DS . basename($readmeFile);
				$readme   .= "\n" . 'Archival Info:' . "\n";
				$readme   .= '>>> ' . basename($readmeFile) . "\n";
				$readme .= "\n ";
				$readme .= "\n ";
				$readme .= '--------------------------------------------' . "\n ";
				$readme .= 'Archival package produced ' . JFactory::getDate()->toSql();

				$handle  = fopen($readmeFile, 'w');
				fwrite($handle, $readme);
				fclose($handle);

				$zip->addFile($readmeFile, $where);
			}

			$zip->close();
		}
		else
		{
		    return false;
		}

		return true;
	}
}