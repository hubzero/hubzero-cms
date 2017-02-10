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

namespace Components\Publications\Models;

use Hubzero\Base\Object;
use Components\Publications\Helpers;
use Components\Publications\Tables;
use stdClass;
use ZipArchive;

// Include building blocks
include_once(__DIR__ . DS . 'blocks.php');
include_once(__DIR__ . DS . 'status.php');
include_once(__DIR__ . DS . 'attachments.php');
include_once(__DIR__ . DS . 'blockelements.php');
include_once(__DIR__ . DS . 'handlers.php');

// Include tables
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'curation.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'curation.history.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'curation.version.php');
require_once(dirname(__DIR__) . DS . 'tables' . DS . 'block.php');

require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'html.php');

/**
 * Publications curation class
 *
 * Parses curation flow into view block for user, admin and curator
 *
 */
class Curation extends Object
{
	/**
	* JDatabase
	*
	* @var object
	*/
	var $_db      		= NULL;

	/**
	* @var    object  Publication
	*/
	var $_pub 			= NULL;

	/**
	* @var    object  Publication model
	*/
	var $_model 		= NULL;

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
	* @var    string Current block blockId
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
	* @param      string  $manifest         Publication manifest
	* @param      string  $masterManifest   Master type manifest
	* @return     void
	*/
	public function __construct( $manifest = NULL, $masterManifest = NULL )
	{
		$this->_db = \App::get('db');

		// Parse blocks
		$this->_setBlocks($manifest, $masterManifest);
	}

	/**
	 * Get blocks in order
	 *
	 * @param      string  $manifest  Publication manifest (version used at time of publication)
	 * @param      string  $masterManifest  Master type manifest (current version)
	 * @return  boolean
	 */
	private function _setBlocks($manifest = NULL, $masterManifest = NULL)
	{
		$blocks   = array();

		if ($masterManifest)
		{
			$masterManifest = json_decode($masterManifest);
		}

		// We need a manifest
		if (!$manifest)
		{
			// Get blocks model
			$blocksModel = new Blocks($this->_db);

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
		}
		else
		{
			$manifest = json_decode($manifest);
		}

		if (empty($manifest))
		{
			return false;
		}

		// Additional parsing
		foreach ($manifest->blocks as $blockId => $b)
		{
			// Get block count
			$this->_blockcount++;

			// Some values like published_editing should come from master manifest
			// This is important for published drafts
			if ($masterManifest)
			{
				if (isset($b->params->published_editing) && isset($masterManifest->blocks->$blockId->params->published_editing))
				{
					$b->params->published_editing = $masterManifest->blocks->$blockId->params->published_editing;
				}

				// Elements settings
				if (!empty($b->elements) && !empty($masterManifest->blocks->$blockId->elements))
				{
					foreach ($b->elements as $elementId => $el)
					{
						if (isset($el->params) && isset($masterManifest->blocks->$blockId->elements->$elementId->params->published_editing))
						{
							$el->params->published_editing = $masterManifest->blocks->$blockId->elements->$elementId->params->published_editing;
						}
					}
				}
			}
		}

		// Top-level params come from master manifest (current version wins)
		if ($masterManifest && isset($masterManifest->params))
		{
			foreach ($masterManifest->params as $param => $value)
			{
				$manifest->params->$param = $value;
			}
		}

		$this->_manifest = $manifest;
		$this->_blocks   = $manifest->blocks;

		return true;
	}

	/**
	 * Get active block
	 *
	 * @param   string  $name		Block name
	 * @param   integer $blockId	Numeric block ID
	 * @return  boolean
	 */
	public function setBlock($name = NULL, $blockId = 0)
	{
		if ($blockId && (!isset($this->_blocks->$blockId)
			|| $this->_blocks->$blockId->name != $name))
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			return false;
		}

		$this->_block 		= $this->_blocks->$blockId;
		$this->_blockname 	= $this->_blocks->$blockId->name;
		$this->_blockorder 	= $blockId;
		return true;
	}

	/**
	 * Get block blockId
	 *
	 * @param   string  $name	Block name
	 * @return  integer
	 */
	public function getBlockId($name = NULL)
	{
		$blockId = NULL;
		foreach ($this->_blocks as $index => $block)
		{
			if ($block->name == $name)
			{
				$blockId = $index;
				break;
			}
		}

		return $blockId;
	}

	/**
	 * getBlockSchema - returns the Blocks of this curation flow 
	 * 
	 * @access public
	 * @return void
	 */
	public function getBlockSchema()
	{
		return $this->_blocks;
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
		$blocksModel = new Blocks($this->_db);

		// Find all blocks of the same parent
		foreach ($this->_blocks as $blockId => $block)
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
	 * @return  array
	 */
	public function getElements( $role = 1, $type = 'file' )
	{
		if (!$this->_blocks)
		{
			return false;
		}

		// Get blocks model
		$blocksModel = new Blocks($this->_db);

		$elements = array();

		// Find all blocks of the same parent
		foreach ($this->_blocks as $blockId => $block)
		{
			$parentBlock = $blocksModel->getBlockProperty($block->name, '_parentname');

			if ($parentBlock == 'content')
			{
				foreach ($block->elements as $elId => $element)
				{
					if ($type && $element->params->type != $type)
					{
						// continue;
					}

					if ($element->params->role == $role)
					{
						$output 			= new stdClass;
						$output->block 		= $block->params;
						$output->blockId 	= $blockId;
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
	 * Get element id by attachment
	 *
	 * @param   integer  $aid		Attachment ID
	 * @return  mixed: object or boolean False
	 */
	public function getElementIdByAttachment($aid = 0)
	{
		if (empty($this->_pub))
		{
			return false;
		}

		// Make sure we got attachments
		$attachments = $this->_pub->attachments();
		if (empty($attachments))
		{
			return false;
		}
		foreach ($attachments['elements'] as $elementId => $rows)
		{
			foreach ($rows as $row)
			{
				if ($row->id == $aid)
				{
					return $elementId;
				}
			}
		}
		return false;
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
		$blocksModel = new Blocks($this->_db);

		// Find all blocks of the same parent
		foreach ($this->_blocks as $blockId => $block)
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
						$output->blockId 	= $blockId;
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
	 * @param   integer $blockId	Numeric block ID
	 * @return  string HTML
	 */
	public function parseBlock( $viewer = 'edit', $name = NULL, $blockId = 0 )
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;

		// Set the block
		if ($name)
		{
			if (!$blockId)
			{
				$blockId = $this->getBlockId($name);
			}

			if (!$blockId)
			{
				$this->setError( Lang::txt('Error loading block') );
				return false;
			}

			$this->_block 		= $this->_blocks->$blockId;
			$this->_blockname 	= $this->_blocks->$blockId->name;
			$this->_blockorder 	= $blockId;
		}

		// Get blocks model
		$blocksModel = new Blocks($this->_db);

		return $blocksModel->renderBlock($this->_blockname, $viewer, $this->_block, $this->_pub, $blockId);
	}

	/**
	 * Set association with publication and load curation
	 *
	 * @param   object  $pub	Models\Publication
	 * @return  void
	 */
	public function setPubAssoc($pub = NULL, $setProgress = true)
	{
		// Set version alias (e.f. 'dev' or 'default')
		if (empty($pub->versionAlias) && isset($pub->version) && !is_object($pub->version))
		{
			$pub->versionAlias = $pub->version;
		}
		$this->_pub = $pub;

		// Set progress
		if ($setProgress)
		{
			$this->setProgress();
		}
	}

	/*----------------------------
		ITEM MANAGEMENT
	*/
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
		$blocksModel = new Blocks($this->_db);

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
			$data->updated 		= Date::toSql();
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
		$blocksModel = new Blocks($this->_db);

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
			$data->updated 		= Date::toSql();
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
		$blocksModel = new Blocks($this->_db);

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
			$data->updated 		= Date::toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
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
		$blocksModel = new Blocks($this->_db);

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
			$data->updated 		= Date::toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/*----------------------------
		CORRESPONDENCE WITH CURATOR
	*/
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
		$dispute  = urldecode(Request::getVar('review', ''));

		if (!trim($dispute))
		{
			$this->setError('Please provide a reason for dispute');
			return false;
		}

		// Record update time
		$data 				= new stdClass;
		$data->updated 		= Date::toSql();
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
		$reason  = urldecode(Request::getVar('review', ''));

		if (!trim($reason))
		{
			$this->setError('Please provide a reason for skipping requirement');
			return false;
		}

		// Record update time
		$data 				 = new stdClass;
		$data->updated 		 = Date::toSql();
		$data->updated_by 	 = $actor;
		$data->review_status = 3;
		$data->update		 = stripslashes($reason);
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

	/*----------------------------
	* BLOCK & ELEMENT MANAGEMENT
	*/
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
		foreach ($this->_blocks as $blockId => $block)
		{
			// Skip inactive blocks
			if (isset($block->active) && $block->active == 0)
			{
				continue;
			}
			$autoStatus 		= self::getStatus($block->name, $this->_pub, $blockId);
			$reviewStatus		= self::getReviewStatus($block->name, $blockId);

			$result->blocks->$blockId 				= new stdClass();
			$result->blocks->$blockId->name 		= $block->name;
			$result->blocks->$blockId->manifest 	= $block;
			$result->blocks->$blockId->firstElement = self::getFirstElement($block->name, $this->_pub, $blockId);

			if ($autoStatus->status > 0)
			{
				$result->lastBlock = $blockId;
			}

			$k++;

			if (($autoStatus->status > 0 && $reviewStatus->status != 0) || $reviewStatus->status == 1 || $reviewStatus->lastupdate)
			{
				$i++;
			}

			// Look at both auto and review status to determine if complete
			if ($reviewStatus)
			{
				if ($block->elements)
				{
					foreach ($block->elements as $elementId => $element)
					{
						if (isset($element->active) && $element->active == 0)
						{
							continue;
						}
						if ($autoStatus->elements->$elementId->status == 0 && $reviewStatus->elements->$elementId->status == 2)
						{
							$i--;
							$reviewStatus->status = 2;
						}
					}
				}
			}
			// Spot a problem
			if (!$result->firstBlock)
			{
				if ($reviewStatus->status == 0 && !$reviewStatus->lastupdate)
				{
					$result->firstBlock = $blockId;
				}
				elseif ($reviewStatus->status == 2 && !$reviewStatus->lastupdate && $autoStatus->status == 0)
				{
					$result->firstBlock = $blockId;
				}
			}

			$result->blocks->$blockId->status 		= $autoStatus;
			$result->blocks->$blockId->review      = $reviewStatus;
		}
		$result->firstBlock = $result->firstBlock ? $result->firstBlock : $blockId;

		// Are all sections complete for submission?
		$result->complete  = $i == $k ? 1 : 0;

		$this->_progress = $result;
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
		foreach ($this->_blocks as $blockId => $block)
		{
			if ($block->name == $name)
			{
				return true;
			}
		}

		return false;
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
	 * @param   integer $blockId	Numeric block ID
	 * @return  integer
	 */
	public function getNextBlock( $name, $blockId = 0, $activeId = 1)
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
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
			if ($id == $blockId)
			{
				$start = 1;
			}
			if ($start == 1 && $id != $blockId)
			{
				$remaining[] = $id;
			}
		}

		// Return element ID
		return empty($remaining) ? $blockId : $remaining[0];
	}

	/**
	 * Determine if block is coming
	 *
	 * @param   string  $name		Block name
	 * @param   integer $blockId	Numeric block ID
	 * @param   integer $activeId	Active block ID
	 * @param   integer $elementId	Element ID in question
	 * @return  boolean
	 */
	public function isBlockComing( $name, $blockId = 0, $activeId = 1)
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
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

		return in_array($blockId, $remaining) ? true : false;
	}

	/**
	 * Get previous block ID
	 *
	 * @param   string  $name		Block name
	 * @param   integer $blockId	Numeric block ID
	 * @return  integer
	 */
	public function getPreviousBlock( $name, $blockId = 0, $activeId = 1)
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
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
			if ($id == $blockId)
			{
				$start = 1;
			}
			if ($start == 0 && $id != $blockId)
			{
				$remaining[] = $id;
			}
		}

		// Return element ID
		return empty($remaining) ? $blockId : end($remaining);
	}

	/**
	 * Check block status (auto check)
	 *
	 * @param   string  $name		Block name
	 * @param   object  $pub		Publication object
	 * @param   integer $blockId	Numeric block ID
	 * @return  object
	 */
	public function getStatus( $name, $pub, $blockId = 0)
	{
		$pub = $pub ? $pub : $this->_pub;

		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
			return false;
		}

		// Get blocks model
		$blocksModel = new Blocks($this->_db);
		return $blocksModel->getStatus($name, $pub, $this->_blocks->$blockId);

		// Return status
		return $status;
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
		$blocksModel = new Blocks($this->_db);

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
			$data->updated 		= Date::toSql();
			$data->updated_by 	= $actor;
			$this->saveUpdate($data, $elementId, $this->_blockname, $this->_pub, $this->_blockorder);
		}

		return true;
	}

	/**
	 * Get first element ID
	 *
	 * @param   string  $name		Block name
	 * @param   object  $pub		Publication object
	 * @param   integer $blockId	Numeric block ID
	 * @return  integer
	 */
	public function getFirstElement( $name, $pub, $blockId = 0)
	{
		$pub = $pub ? $pub : $this->_pub;
		$elementId = 0;

		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
			return $elementId;
		}

		if ($this->_blocks->$blockId->elements)
		{
			foreach ($this->_blocks->$blockId->elements as $id => $element)
			{
				return $id;
			}
		}

		// Return status
		return $elementId;
	}

	/**
	 * Get next element ID
	 *
	 * @param   string  $name		Block name
	 * @param   integer $blockId	Numeric block ID
	 * @param   integer $activeId	Active element ID
	 * @return  integer
	 */
	public function getNextElement( $name, $blockId = 0, $activeId = 1)
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		if ($this->_blocks->$blockId->elements)
		{
			foreach ($this->_blocks->$blockId->elements as $id => $element)
			{
				if (isset($element->active) && $element->active == 0)
				{
					continue;
				}
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
	 * @param   integer $blockId	Numeric block ID
	 * @param   integer $activeId	Active element ID
	 * @param   integer $elementId	Element ID in question
	 * @return  boolean
	 */
	public function isComing( $name, $blockId = 0, $activeId = 1, $elementId = 0)
	{
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
			return $activeId;
		}

		$remaining = array();
		$start	   = 0;
		if ($this->_blocks->$blockId->elements)
		{
			foreach ($this->_blocks->$blockId->elements as $id => $element)
			{
				if (isset($element->active) && $element->active == 0)
				{
					continue;
				}
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
	 * @param   integer $blockId	Numeric block ID
	 * @return  object
	 */
	public function getElementStatus( $name, $elementId = NULL, $pub, $blockId = 0)
	{
		$pub = $pub ? $pub : $this->_pub;

		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$blockId)
		{
			$this->setError( Lang::txt('Error loading block') );
			return false;
		}

		// Get blocks model
		$blocksModel 	= new Blocks($this->_db);
		return $blocksModel->getStatus($name, $pub, $this->_blocks->$blockId, $elementId );
	}

	/*----------------------------
	* CURATION REVIEW
	*/
	/**
	 * Check status for curation review
	 *
	 * @param   string  $block		Block name
	 * @param   integer $blockId	Numeric block ID
	 * @return  object
	 */
	public function getReviewStatus( $block, $blockId = 0)
	{
		// Get status model
		$status = new Status();

		if (!isset($this->_pub->reviewedItems))
		{
			$this->_pub->reviewedItems = $this->getReviewedItems();
		}

		$manifest = $this->_blocks->$blockId;

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
				if (isset($element->active) && $element->active == 0)
				{
					continue;
				}
				$props = $block . '-' . $blockId . '-' . $elementId;

				if (!isset($status->elements))
				{
					$status->elements = new stdClass();
				}
				$status->elements->$elementId = $this->getReviewItemStatus( $props, $this->_pub->reviewedItems);

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
			$passed 	    	= ($success == $i || $this->_pub->state == 1) ? 1 : 0;
			$status->status 	= $failed > 0 ? 0 : $passed;
			$status->status 	= ($incomplete  && !$failed) ? 2 : $status->status; // unreviewed
			$status->status 	= ($skipped > 0 && ($skipped + $incomplete) == $i) ? 3 : $status->status; // skipped
			$status->lastupdate = ($pending > 0 || $skipped > 0) && $passed == 1 ? true : NULL;
		}
		else
		{
			$props = $block . '-' . $blockId;
			return $this->getReviewItemStatus( $props, $this->_pub->reviewedItems);
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
		$status             = new Status();
		$status->status     = 2; // unreviewed
		$status->updated_by = 0;

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
		if ($status->status == 3)
		{
			$status->lastupdate = $record->updated;
			$status->updated_by = $record->updated_by;
		}
		if ($record->review_status == 3 || ($record->reviewed && $record->update
			&& strtotime($record->updated) > strtotime($record->reviewed))
		)
		{
			$status->message = $record->update;
		}

		return $status;
	}

	/**
	 * Parse curation status for display
	 *
	 * @param   object  $pub	Publication object
	 * @param   integer $step	Numeric block ID
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

		$status->status        = $reviewStatus->status;
		$status->curatornotice = $reviewStatus->getError();
		$status->updated       = $pub->state != 1 ? $reviewStatus->lastupdate : NULL;
		$status->authornotice  = $reviewStatus->message;

		if ($status->updated && isset($reviewStatus->updated_by) && $reviewStatus->updated_by)
		{
			$profile = User::getInstance($reviewStatus->updated_by);
			$by = ' ' . Lang::txt('COM_PUBLICATIONS_CURATION_BY') . ' ' . $profile->get('name');

			if ($status->status != 3)
			{
				$status->updatenotice = Lang::txt('COM_PUBLICATIONS_CURATION_UPDATED') . ' ' . Date::of($status->updated)->format('M d, Y @ H:i') . $by;
			}
			else
			{
				$status->updatenotice = Lang::txt('COM_PUBLICATIONS_CURATION_SKIPPED') . ' ' . $by;
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
		<?php if ($viewer == 'curator') { ?>
		<span class="edit-notice">[<a href="#">edit</a>]</span>
		<?php } ?>
		<?php if (($viewer == 'author' && (!$curatorStatus->curatornotice && $curatorStatus->status == 3)) ) { return; } ?>
		<div class="status-notice">
			<span class="update-notice"><?php if ($viewer == 'curator') { echo  $curatorStatus->updatenotice; }
			elseif ($curatorStatus->status != 3) {
				if ($curatorStatus->authornotice && $curatorStatus->updated)
				{
					?>
						<span class="dispute-notice">
							<span class="remove-notice" id="<?php echo $props; ?>">[<a href="#<?php echo $elName; ?>"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_DISPUTE_DELETE'); ?></a>]</span>
							<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_DISPUTE_NOTICE'); ?>
							<span class="dispute-text"><?php echo $curatorStatus->authornotice; ?></span>
						</span>
				<?php }
				else
				{
					echo  Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_UPDATED');
				}
			} ?></span>
			<?php if ($viewer == 'author' && $curatorStatus->curatornotice && !$curatorStatus->updated) {  ?>
			<span class="disputeit" id="<?php echo $props; ?>">[<a href="#<?php echo $elName; ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CURATION_DISPUTE_THIS'); ?></a>]</span>
			<?php } ?>

			<span class="fail-notice"><?php echo $viewer == 'curator' ? Lang::txt('COM_PUBLICATIONS_CURATION_NOTICE_TO_AUTHORS') : Lang::txt('COM_PUBLICATIONS_CURATION_CHANGE_REQUEST'); ?></span>
			<span class="notice-text"><?php echo $curatorStatus->curatornotice; ?></span>
			<?php if ($curatorStatus->authornotice && $viewer == 'curator') { ?>
			<span class="dispute-notice">
				<strong><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_DISPUTE_NOTICE'); ?></strong>
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
			<span class="checker-pass <?php echo ($status == 1) ? 'picked' : ''; ?><?php echo $updated ? ' updated' : ''; ?>"><a href="<?php echo $url; ?>" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_APPROVE'); ?>"></a></span>
			<span class="checker-fail <?php echo $status == 0 ? 'picked' : ''; ?><?php echo $updated ? ' updated' : ''; ?>"><a href="#addnotice" title="<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_NOT_APPROVE'); ?>"></a></span>
		</div>
	<?php
	}

	/**
	 * Get curation reviews for version ID
	 *
	 * @param   integer  $versionId
	 * @return  array or boolean False
	 */
	public function getReviewedItems()
	{
		if (empty($this->_pub->version_id))
		{
			return false;
		}

		$review = array();

		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\Curation($this->_db);
		}
		$results = $this->_tbl->getRecords($this->_pub->version_id);

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

	/*----------------------------
	* CURATION HISTORY
	*/
	/**
	 * Get history logs
	 *
	 * @param   array $filters
	 * @return  object or NULL
	 */
	public function getHistory($filters = array())
	{
		if (empty($this->_pub))
		{
			return false;
		}
		if (!isset($this->_tblHistory))
		{
			$this->_tblHistory = new Tables\CurationHistory($this->_db);
		}

		$history = $this->_tblHistory->getRecords($this->_pub->version_id, $filters);

		return $history;
	}

	/**
	 * Get last history log
	 *
	 * @return  object or NULL
	 */
	public function getLastHistoryRecord()
	{
		if (empty($this->_pub))
		{
			return false;
		}
		if (!isset($this->_tblHistory))
		{
			$this->_tblHistory = new Tables\CurationHistory($this->_db);
		}

		$history = $this->_tblHistory->getLastRecord($this->_pub->version_id);

		return $history;
	}

	/**
	 * Get change log
	 *
	 * @param   integer $oldStatus	Previous version state
	 * @param   integer $newStatus	New version state
	 * @param   integer $curator	Author or curator
	 * @return  string
	 */
	public function getChangeLog( $oldStatus = 0, $newStatus = 0, $curator = 0 )
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
		if (!empty($this->_progress) && ($newStatus == 7 || $oldStatus == 7))
		{
			$changelog .= '<hr />';
			$changelog .= $newStatus == 7
						? '<p>Changes requested for sections: </p>'
						: '<p>Updated sections include: </p>';
			$changelog .= '<ul>';
			foreach ($this->_progress->blocks as $blockId => $block)
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
	 * @param   integer $actor		Actor user ID
	 * @param   integer $oldStatus	Previous version state
	 * @param   integer $newStatus	New version state
	 * @param   integer $curator	Author or curator
	 * @return  boolean
	 */
	public function saveHistory( $actor = 0, $oldStatus = 0, $newStatus = 0, $curator = 0 )
	{
		if (empty($this->_pub))
		{
			return false;
		}

		// Incoming
		$comment = Request::getVar('comment', '', 'post');

		// Collect details
		$changelog = $this->getChangeLog($oldStatus, $newStatus, $curator);

		if (!$changelog)
		{
			return false;
		}

		$obj = new Tables\CurationHistory($this->_db);

		// Create new record
		$obj->publication_version_id 	= $this->_pub->version_id;
		$obj->created 					= Date::toSql();
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
	 * Get last curation update
	 *
	 * @param   integer  $elementId		Element ID
	 * @param   string   $name			Block name
	 * @param   integer  $blockId		Numeric block ID
	 * @return boolean
	 */
	public function getLastUpdate( $elementId, $name, $blockId )
	{
		if (empty($this->_pub))
		{
			return false;
		}
		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\Curation($this->_db);
		}

		return $this->_tbl->getRecord(
			$this->_pub->id,
			$this->_pub->version_id,
			$name,
			$blockId,
			$elementId
		);
	}

	/**
	 * Save update
	 *
	 * @param   object   $data			Data to save
	 * @param   integer  $elementId		Element ID
	 * @param   string   $name			Block name
	 * @param   object   $pub			Publication object
	 * @param   integer  $blockId		Numeric block ID
	 * @return boolean
	 */
	public function saveUpdate( $data = NULL, $elementId, $name, $pub, $blockId )
	{
		if ($data === NULL)
		{
			return false;
		}

		$name 	  = $name ? $name : $this->_blockname;
		$pub 	  = $pub ? $pub : $this->_pub;
		$blockId = $blockId ? $blockId : $this->_blockorder;
		if (!$blockId)
		{
			$blockId = $this->getBlockId($name);
		}

		if (!$pub || !$name || !$blockId)
		{
			return false;
		}

		if (!isset($this->_tbl))
		{
			$this->_tbl = new Tables\Curation($this->_db);
		}

		// Load curation record if exists
		if ($this->_tbl->loadRecord($pub->id, $pub->version_id, $name, $blockId, $elementId))
		{
			// Record found - update
		}
		else
		{
			// Create new record
			$this->_tbl->publication_id 		= $pub->id;
			$this->_tbl->publication_version_id = $pub->version_id;
			$this->_tbl->block 					= $name;
			$this->_tbl->step					= $blockId;
			$this->_tbl->element				= $elementId;
		}

		// Insert incoming data
		foreach ($data as $field => $value)
		{
			$field = trim($field);
			$this->_tbl->$field = trim($value);
		}

		if ($this->_tbl->store())
		{
			return true;
		}

		return false;
	}

	/*----------------------------
	* PACKAGING
	*/
	/**
	 * Produce publication package
	 *
	 * @return     boolean
	 */
	public function showPackageContents()
	{
		if (!$this->_pub)
		{
			return false;
		}

		// Get elements
		$prime    = $this->getElements(1);
		$second   = $this->getElements(2);
		$gallery  = $this->getElements(3);
		$elements = array_merge($prime, $second, $gallery);

		// Do we have items to package?
		if (!$elements)
		{
			return '<p class="witherror">' . Lang::txt('COM_PUBLICATIONS_CURATION_PACKAGE_ERROR_NO_FILES') . '</p>';
		}

		// Get attachment type model
		$attModel = new Attachments($this->_db);
		$contents = '<ul class="filelist">';

		$contents .= $attModel->showPackagedItems(
			$elements,
			$this->_pub
		);

		// Custom license to be included in LICENSE.txt
		if ($this->_pub->license_text)
		{
			$contents .= '<li>' . \Components\Projects\Models\File::drawIcon('txt') . ' LICENSE.txt</li>';
		}
		$contents .= '<li>' . \Components\Projects\Models\File::drawIcon('txt') . ' README.txt</li>';
		$contents .= '</ul>';

		return $contents;
	}

	/**
	 * Get bundle package name
	 *
	 * @return     boolean
	 */
	public function getBundleName()
	{
		if (empty($this->_pub))
		{
			return false;
		}

		return Lang::txt('Publication') . '_' . $this->_pub->id . '.zip';
	}

	/**
	 * Serve publication package
	 *
	 * @return     boolean
	 */
	public function serveBundle()
	{
		if (empty($this->_pub))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_FILE_NOT_FOUND'), 404);
			return;
		}

		$bundle = $this->_pub->path('base', true) . DS . $this->getBundleName();
		$doi = $this->_pub->version->get('doi');

		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$serveas = $doi . '.zip';
		}
		else
		{
			// Already contains a '.zip' on the end.
			$serveas = $this->getBundleName();
		}

		if (!is_file($bundle))
		{
			throw new Exception(Lang::txt('COM_PUBLICATIONS_FILE_NOT_FOUND'), 404);
			return;
		}

		// Initiate a new content server and serve up the file
		$server = new \Hubzero\Content\Server();
		$server->filename($bundle);
		$server->disposition('download');
		$server->acceptranges(true);
		$server->saveas($serveas);

		if (!$server->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_PUBLICATIONS_SERVER_ERROR'), 404);
		}
		else
		{
			exit;
		}

		return;

	}

	/**
	 * Produce publication package
	 *
	 * @return     boolean
	 */
	public function package()
	{
		if (empty($this->_pub))
		{
			return false;
		}

		// Get elements
		$prime    = $this->getElements(1);
		$second   = $this->getElements(2);
		$gallery  = $this->getElements(3);
		$elements = array_merge($prime, $second, $gallery);

		// Do we have items to package?
		if (!$elements)
		{
			return false;
		}
		if (!is_dir($this->_pub->path('base', true)))
		{
			return false;
		}

		// Use DOI if available
		$doi = $this->_pub->version->get('doi');
		if ($doi != '')
		{
			$doi = str_replace('.', '_', $doi);
			$doi = str_replace('/', '_', $doi);
			$bundleName = $doi;
		}
		else
		{
			$bundleName = rtrim($this->getBundleName(), '.zip');
		}

		// Set archival properties
		$bundleDir  = $bundleName;
		$tarname 	= $this->getBundleName();
		$tarpath 	= $this->_pub->path('base', true) . DS . $tarname;
		$licFile 	= $this->_pub->path('base', true) . DS . 'LICENSE.txt';
		$readmeFile = $this->_pub->path('base', true) . DS . 'README.txt';

		// Get attachment type model
		$attModel = new Attachments($this->_db);

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
		$objL = new Tables\License( $this->_db );
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
				$readme .= 'Archival package produced ' . Date::toSql();

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

	/*----------------------------
	* COVERSION, TRANSFER, MISC
	*/
	/**
	 * Conversion for publications created in a non-curated flow
	 *
	 * @param   object $pub
	 * @return  boolean
	 */
	public function convertToCuration( $pub = NULL )
	{
		$pub     = $pub ? $pub : $this->_pub;
		$oldFlow = false;

		// Load attachments
		$pub->attachments();

		if (!isset($pub->_attachments)
			|| empty($pub->_attachments['elements']))
		{
			// Nothing to convert
			return false;
		}

		// Get supporting docs element manifest
		$sElements = self::getElements(2);
		$sElement  = $sElements ? $sElements[0] : NULL;

		// Loop through attachments
		foreach ($pub->_attachments['elements'] as $elementId => $elementAttachments)
		{
			if (empty($elementAttachments))
			{
				continue;
			}
			// Check if any attachments are missing element id
			foreach ($elementAttachments as $elAttach)
			{
				if ($elAttach->element_id == 0)
				{
					// Save elementid
					$row = new Tables\Attachment( $this->_db );
					if ($row->load($elAttach->id))
					{
						$markId = $elAttach->role != 1 && $sElement ? $sElement->id : $elementId;
						$row->element_id = $markId;
						$row->store();
					}
					$oldFlow = true; // will need to make further checks
				}
			}
		}

		if (!$oldFlow)
		{
			return false;
		}

		// Get gallery element manifest
		$elements = self::getElements(3);
		$element = $elements ? $elements[0] : NULL;

		// Retrieve screenshots
		$pScreenshot = new Tables\Screenshot( $this->_db );
		$shots = $pScreenshot->getScreenshots( $pub->version_id );

		// Transfer gallery files to the right location
		if ($element && $shots)
		{
			// Get attachment type model
			$attModel = new Attachments($this->_db);
			$fileAttach = $attModel->loadAttach('file');

			// Set configs
			$configs  = $fileAttach->getConfigs(
				$element->manifest->params,
				$element->id,
				$pub,
				$element->block
			);

			// Get gallery path
			$galleryPath 	= Helpers\Html::buildPubPath(
				$pub->id,
				$pub->version_id,
				'',
				'gallery',
				1
			);

			if (is_dir($galleryPath))
			{
				foreach ($shots as $shot)
				{
					$objPA = new Tables\Attachment( $this->_db );
					if (is_file($galleryPath . DS . $shot->srcfile)
					&& !$objPA->loadElementAttachment($pub->version_id, array( 'path' => $shot->filename),
						$element->id, 'file', $element->manifest->params->role))
					{
						$objPA = new Tables\Attachment( $this->_db );
						$objPA->publication_id 			= $pub->id;
						$objPA->publication_version_id 	= $pub->version_id;
						$objPA->path 					= $shot->filename;
						$objPA->type 					= 'file';
						$objPA->created_by 				= User::get('id');
						$objPA->created 				= Date::toSql();
						$objPA->role 					= $element->manifest->params->role;
						$objPA->element_id 				= $element->id;
						$objPA->ordering 				= $shot->ordering;
						if (!$objPA->store())
						{
							continue;
						}
						// Check if names is already used
						$suffix = $fileAttach->checkForDuplicate(
							$configs->path . DS . $objPA->path,
							$objPA,
							$configs
						);
						// Save params if applicable
						if ($suffix)
						{
							$objPA->params = 'suffix=' . $suffix . "\n";
						}

						// Copy file into the right spot
						$configs->copyFrom = $galleryPath . DS . $shot->srcfile;
						if (!$fileAttach->publishAttachment($objPA, $pub, $configs))
						{
							$objPA->delete();
						}
					}
				}
			}
		}

		// Check if published version has curation manifest saved
		$row = new Tables\Version( $this->_db );
		if ($pub->state == 1 && !$pub->curation)
		{
			if ($row->load($pub->version_id))
			{
				$row->curation = json_encode($this->_manifest);
				$row->store();
			}
		}
		// Mark as curated
		$row->saveParam($row->id, 'curated', 1);

		return true;
	}

	/**
	 * Transfer content from one version to another
	 *
	 * @param   object  $old	Transfer from version record
	 * @param   object  $new	Transfer to version record
	 * @return  boolean
	 */
	public function transfer( $pub, $old, $new)
	{
		// Get blocks model
		$blocksModel = new Blocks($this->_db);

		foreach ($pub->_curationModel->_progress->blocks as $blockId => $block)
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

		$row = new Tables\Version( $this->_db );

		// Incoming
		$label = trim(Request::getVar( 'label', '', 'post' ));
		$used_labels = $row->getUsedLabels( $this->_pub->id, $this->_pub->version_number );

		if ($label && in_array($label, $used_labels))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_USED') );
			return false;
		}
		elseif ($label)
		{
			if (!$row->loadVersion($this->_pub->id, $this->_pub->version_number))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_ERROR') );
				return false;
			}

			$row->version_label = $label;
			if (!$row->store())
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_LABEL_ERROR') );
			}
		}

		// Success message
		$this->set('_message', Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION_LABEL_SAVED'));

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
	 * Get curation manifest version
	 *
	 * @return  string HTML
	 */
	public function getCurationVersion($id = 0)
	{
		if (!isset($this->_curationVersion) || $id != $this->_curationVersion->id)
		{
			$this->_curationVersion = new Tables\CurationVersion($this->_db);
			if (intval($id) > 0)
			{
				$this->_curationVersion->load($id);
			}
			else
			{
				// Load latest by type
				$this->_curationVersion->loadLatest($this->_pub->get('master_type'));
			}
		}

		return $this->_curationVersion;
	}

	/**
	 * Save curation manifest version if new, return latest id
	 *
	 * @return  string HTML
	 */
	public function checkCurationVersion()
	{
		// Get current master type manifest
		$manifest = $this->_pub->masterType()->curation;

		// Get saved current version
		$current = $this->getCurationVersion();

		// Save this version if changed
		if (!$current || $current->curation != $manifest)
		{
			$versionNumber = $current ? $current->version_number + 1 : 1;
			$this->_curationVersion                 = new Tables\CurationVersion($this->_db);
			$this->_curationVersion->type_id        = $this->_pub->get('master_type');
			$this->_curationVersion->curation       = $manifest;
			$this->_curationVersion->created        = Date::toSql();
			$this->_curationVersion->version_number = $versionNumber;
			if ($this->_curationVersion->store())
			{
				return $this->_curationVersion->id;
			}
		}

		return $current->id;
	}
}
