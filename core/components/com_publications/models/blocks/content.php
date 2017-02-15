<?php
/**
 * @package		HUBzero CMS
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

/**
 * Content block
 */
class Content extends Base
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name 			= 'content';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected	$_parentname 	= 'content';

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected	$_manifest 		= NULL;

	/**
	* Numeric block ID
	*
	* @var		integer
	*/
	protected	$_blockId = 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = NULL, $manifest = NULL, $viewname = 'edit', $blockId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Register blockId
		$this->_blockId	= $blockId;

		if ($viewname == 'curator')
		{
			// Output HTML
			$view = new \Hubzero\Component\View(
				array(
					'name'		=> 'curation',
					'layout'	=> 'block'
				)
			);
		}
		else
		{
			$name = $viewname == 'freeze' ? 'freeze' : 'draft';

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=> 'projects',
					'element'	=> 'publications',
					'name'		=> $name,
					'layout'	=> 'wrapper'
				)
			);
		}

		// Make sure we have attachments
		$pub->attachments();

		// Get block status
		$status = $pub->curation('blocks', $blockId, 'status');
		$status->review = $pub->curation('blocks', $blockId, 'review');

		// Get block element model
		$elModel = new \Components\Publications\Models\BlockElements($this->_parent->_db);

		// Properties object
		$master 			= new stdClass;
		$master->block 		= $this->_name;
		$master->blockId 	= $this->_blockId;
		$master->params		= $this->_manifest->params;
		$master->props		= $elModel->getActiveElement($status->elements, $status->review);

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent( $pub, $viewname, $status, $master );
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $blockId;
		$view->showControls	= isset($master->params->collapse_elements)
							&& $master->params->collapse_elements == 1 ? 3 : 2;
		$view->status		= $status;
		$view->master		= $master;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block content
	 *
	 * @return  string  HTML
	 */
	public function save( $manifest = NULL, $blockId = 0, $pub = NULL, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Make sure changes are allowed
		if ($this->_parent->checkFreeze($this->_manifest->params, $pub))
		{
			return false;
		}

		// Make sure we have current attachments
		$pub->attachments();

		// Save each element
		$saved = 0;
		foreach ($this->_manifest->elements as $id => $element)
		{
			// Are we saving just one element?
			if ($elementId && $id != $elementId)
			{
				continue;
			}

			if ($this->saveElement($id, $element->params, $pub, $this->_manifest->params))
			{
				$saved++;

				if ($this->get('_update'))
				{
					$lastRecord = $pub->_curationModel->getLastUpdate($id, $this->_name, $blockId);

					// Record update time
					$data 				= new stdClass;
					$data->updated 		= Date::toSql();
					$data->updated_by 	= $actor;
					$data->update 		= ''; // remove dispute

					// Unmark as skipped
					if ($lastRecord && $lastRecord->review_status == 3)
					{
						$data->review_status = 0;
						$data->update = '';
					}
					$pub->_curationModel->saveUpdate($data, $id, $this->_name, $pub, $blockId);
				}
			}
		}

		// Set success message
		$this->_parent->set('_message', $this->get('_message'));

		return true;
	}

	/**
	 * Transfer data from one version to another
	 *
	 * @return  boolean
	 */
	public function transferData( $manifest, $pub, $oldVersion, $newVersion )
	{
		// Make sure we have attachments
		$pub->attachments();

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Transfer data of each element
		foreach ($manifest->elements as $elementId => $element)
		{
			$attModel->transferData($element->params->type, $element,
				$elementId, $pub, $manifest->params,
				$oldVersion, $newVersion
			);
		}
	}

	/**
	 * Build panel content
	 *
	 * @return  string  HTML
	 */
	public function buildContent( $pub = NULL, $viewname = 'edit', $status, $master )
	{
		$html = '';

		// Get selector styles
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications','selector');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'links');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files','selector');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'databases','selector');

		// Get block element model
		$elModel = new \Components\Publications\Models\BlockElements($this->_parent->_db);

		// Build each element
		$o = 1;
		foreach ($this->_manifest->elements as $elementId => $element)
		{
			$html  .= $elModel->drawElement(
						$element->name, $elementId, $element, $master,
						$pub, $status, $viewname, $o
			);
			$o++;
		}

		return $html;
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $pub = NULL, $manifest = NULL, $elementId = NULL )
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Make sure we have attachments
		$pub->attachments();

		// Start status
		$status = new \Components\Publications\Models\Status();

		// Check against manifested requirements
		if ($this->_manifest)
		{
			// Get attachment elements from manifest
			$mAttach = array();
			foreach ($this->_manifest->elements as $id => $element)
			{
				if ($element->type == 'attachment')
				{
					$mAttach[$id] = $element->params;
				}
			}

			// Return element status
			if ($elementId && isset($mAttach[$elementId]))
			{
				return self::getElementStatus($elementId, $mAttach[$elementId], $pub->_attachments);
			}

			// Check if requirements are satisfied for each attachment element
			$i 		 	= 0;
			$success 	= 0;
			$incomplete = 0;
			foreach ($mAttach as $elementId => $elementparams)
			{
				if (!isset($status->elements))
				{
					$status->elements = new stdClass();
				}
				$status->elements->$elementId = self::getElementStatus($elementId, $elementparams, $pub->_attachments);

				if ($status->elements->$elementId->status >= 1)
				{
					$success++;
				}
				if ($status->elements->$elementId->status == 2)
				{
					$incomplete++;
				}

				$i++;
			}

			$success = $success == $i ? 1 : 0;
			$status->status = $success == 1 && $incomplete ? 2 : $success;
		}

		return $status;
	}

	/**
	 * Check element status
	 *
	 * @return  object
	 */
	public function getElementStatus( $elementId, $elementparams, $attachments = NULL )
	{
		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		$status = $attModel->getStatus( $elementparams->type, $elementparams, $elementId, $attachments );
		return $status;
	}

	/**
	 * Save element
	 *
	 * @return  object
	 */
	public function saveElement( $elementId, $elementparams, $pub = NULL, $params = NULL )
	{
		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		if ($attModel->attach( $elementparams->type, $elementparams, $elementId, $pub, $params ))
		{
			// Pick up status message
			if ($attModel->get('_message'))
			{
				$this->set('_message', $attModel->get('_message'));
			}

			// Set request to update curation record
			if ($attModel->get('_update'))
			{
				$this->set('_update', 1);
			}

			return true;
		}

		// Pick up attachment error messages
		if ($attModel->getError())
		{
			$this->setError($attModel->getError());
		}

		return false;
	}

	/**
	 * Update attachment record
	 *
	 * @return  void
	 */
	public function saveItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : Request::getInt( 'aid', 0 );

		// Load attachment
		$row = new \Components\Publications\Tables\Attachment( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
			return false;
		}

		// Attachment type
		$type = $row->type;

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Save incoming attachment info
		$attModel->update($type, $row, $pub, $actor, $elementId,
			$manifest->elements->$elementId, $manifest->params);

		// Set success message
		if ($attModel->get('_message'))
		{
			$this->set('_message', $attModel->get('_message'));
		}

		// Set request to update curation record
		if ($attModel->get('_update'))
		{
			$this->_parent->set('_update', 1);
		}

		return true;
	}

	/**
	 * Delete attachment record
	 *
	 * @return  void
	 */
	public function deleteItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : Request::getInt( 'aid', 0 );

		// Load attachment
		$row = new \Components\Publications\Tables\Attachment( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid))
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
			return false;
		}

		// Attachment type
		$type = $row->type;

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Save incoming attachment info
		$attModel->remove($type, $row, $pub, $actor, $elementId,
			$manifest->elements->$elementId, $manifest->params);

		// Set success message
		if ($attModel->get('_message'))
		{
			$this->set('_message', $attModel->get('_message'));
		}

		// Set request to update curation record
		if ($attModel->get('_update'))
		{
			$this->_parent->set('_update', 1);
		}

		return true;
	}

	/**
	 * Get default manifest for the block element
	 *
	 * @return  void
	 */
	public function getElementManifest()
	{
		$manifest = array (
			'name'		=> 'dataselector',
			'type' 		=> 'attachment',
			'label'		=> 'Add file(s)',
			'about'		=> '<p>Select a file or a number of files from the project repository</p>',
			'aboutProv'	=> '<p>Attach a file or a number of files to be bundled together</p>',
			'adminTips'	=> '',
			'params' 	=> array (
				'type'			=> 'file',
				'title'			=> '',
				'required' 		=> 0,
				'min' 			=> 0,
				'max' 			=> 500,
				'role' 			=> 2,
				'typeParams'	=> array(
					'allowed_ext' 		=> array(),
					'required_ext'  	=> array(),
					'handler' 			=> NULL,
					'handlers'			=> NULL,
					'directory'			=> '',
					'reuse' 			=> 0,
					'dirHierarchy' 		=> 1,
					'multiZip'			=> 1
				)
			)
		);
		return json_decode(json_encode($manifest), FALSE);
	}

	/**
	 * Get default manifest for the block
	 *
	 * @return  void
	 */
	public function getManifest($new = false)
	{
		// Load config from db
		$obj = new \Components\Publications\Tables\Block($this->_parent->_db);
		$manifest = $obj->getManifest($this->_name);

		// Fall back
		if (!$manifest)
		{
			$manifest = array(
				'name' 			=> 'content',
				'label' 		=> 'Content',
				'title' 		=> 'Publication Content',
				'draftHeading' 	=> 'Let\'s work on publication content',
				'draftTagline'	=> 'Here is what\'s required:',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 		=> array(
					1 => array (
						'name'		=> 'dataselector',
						'type' 		=> 'attachment',
						'label'		=> 'Primary File(s)',
						'about'		=> '<p>Select a file or a number of files from the project repository</p>',
						'aboutProv'	=> '<p>Attach a file or a number of files to be bundled together</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'type'			=> 'file',
							'title'			=> '',
							'required' 		=> 1,
							'min' 			=> 1,
							'max' 			=> 500,
							'role' 			=> 1,
							'typeParams'	=> array(
								'allowed_ext' 		=> array(),
								'required_ext'  	=> array(),
								'handler' 			=> NULL,
								'handlers'			=> array(),
								'directory'			=> '',
								'reuse' 			=> 0,
								'dirHierarchy' 		=> 1,
								'multiZip'			=> 1
							)
						)
					)
				),
				'params'	=> array(
					'required' 			=> 1,
					'published_editing' => 0,
					'collapse_elements' => 1,
					'verify_types'		=> 1
				)
			);

			if ($new == true)
			{
				$manifest['elements'] = array(1 => $this->getElementManifest());
			}

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}
