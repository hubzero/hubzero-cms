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
 * Description block
 */
class Description extends Base
{
	/**
	 * Block name
	 *
	 * @var		string
	 */
	protected $_name = 'description';

	/**
	 * Parent block name
	 *
	 * @var		string
	 */
	protected $_parentname = 'description';

	/**
	 * Default manifest
	 *
	 * @var		string
	 */
	protected $_manifest = null;

	/**
	 * Numeric block ID
	 *
	 * @var		integer
	 */
	protected $_blockId = 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display($pub = null, $manifest = null, $viewname = 'edit', $blockId = 0)
	{
		// Set block manifest
		if ($this->_manifest === null)
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
		$view->content 		= self::buildContent($pub, $viewname, $status, $master);
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $blockId;
		$view->showControls	= isset($master->params->collapse_elements)
							&& $master->params->collapse_elements == 1 ? 3 : 1;
		$view->status		= $status;
		$view->master		= $master;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block
	 *
	 * @return  string  HTML
	 */
	public function save($manifest = null, $blockId = 0, $pub = null, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === null)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Make sure changes are allowed
		if ($this->_parent->checkFreeze($this->_manifest->params, $pub))
		{
			return false;
		}

		// Load publication version
		$row = new \Components\Publications\Tables\Version($this->_parent->_db);

		if (!$row->load($pub->version_id))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'));
			return false;
		}

		// Track changes
		$changed  = 0;
		$missed   = 0;
		$collapse = $this->_manifest->params->collapse_elements == 0 ? 0 : 1;

		// Incoming
		$nbtags = Request::getArray('nbtag', array());

		// Parse metadata
		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $pub->metadata, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = \Components\Publications\Helpers\Html::_txtUnpee($match[2]);
			}
		}

		// Save each element
		foreach ($this->_manifest->elements as $id => $element)
		{
			// Are we saving just one element?
			if ($elementId && $id != $elementId && $collapse)
			{
				continue;
			}

			$field 	  = $element->params->field;
			$aliasmap = $element->params->aliasmap;
			$input 	  = $element->params->input;
			$required = $element->params->required;

			if ($field == 'metadata')
			{
				$value = isset($nbtags[$aliasmap]) ? trim(stripslashes($nbtags[$aliasmap])) : null;

				if (!$value && $required)
				{
					$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
				}
				else
				{
					if (($value && !isset($data[$aliasmap])) || (isset($data[$aliasmap]) && $data[$aliasmap] != $value))
					{
						$changed++;
					}

					// Replace data
					$data[$aliasmap] = $value;

					// Save all in one field
					$tagCollect = '';
					foreach ($data as $tagname => $tagcontent)
					{
						$tagCollect .= "\n".'<nb:' . $tagname . '>' . $tagcontent . '</nb:' . $tagname . '>' . "\n";
					}

					$row->metadata = $tagCollect;
				}
			}
			else
			{
				$value = trim(Request::getString($field, '', 'post'));
				$value = ($input == 'editor')
					? stripslashes($value)
					: \Hubzero\Utility\Sanitize::clean($value);

				if (!$value && $required)
				{
					$missed++;
				}
				if ($row->$field != $value)
				{
					$lastRecord = $pub->_curationModel->getLastUpdate($id, $this->_name, $blockId);
					$changed++;

					// Record update time
					$data = new stdClass;
					$data->updated    = Date::toSql();
					$data->updated_by = $actor;

					// Unmark as skipped
					if ($lastRecord && $lastRecord->review_status == 3)
					{
						$data->review_status = 0;
						$data->update = '';
					}
					if ($value)
					{
						$data->update = ''; // remove dispute message if requirement satisfied
					}
					$pub->_curationModel->saveUpdate($data, $id, $this->_name, $pub, $blockId);
				}
				$row->$field = $value;
			}
		}

		// Update modified info
		if ($changed)
		{
			$row->modified 	  = Date::toSql();
			$row->modified_by = $actor;
			$this->_parent->set('_update', 1);
		}

		// Report error
		if ($missed && $collapse == 0)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
		}

		// Save
		if (!$row->store())
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_ERROR_SAVE_PUBLICATION'));
			return false;
		}

		// Set success message
		$this->_parent->set('_message', $this->get('_message'));

		return true;
	}

	/**
	 * Build panel content
	 * Draw each manifested element
	 *
	 * @return  string  HTML
	 */
	public function buildContent($pub = null, $viewname = 'edit', $status, $master)
	{
		// Get block element model
		$elModel = new \Components\Publications\Models\BlockElements($this->_parent->_db);

		$html = '';

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
	public function getStatus($pub = null, $manifest = null, $elementId = null)
	{
		// Set block manifest
		if ($this->_manifest === null)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Start status
		$status = new \Components\Publications\Models\Status();

		// Return element status
		if ($elementId !== null && isset($this->_manifest->elements->$elementId))
		{
			return self::getElementStatus($this->_manifest->elements->$elementId, $pub);
		}

		// Check against manifested requirements
		if ($this->_manifest && $this->_manifest->elements)
		{
			// Check if requirements are satisfied for each attachment element
			$i          = 0;
			$success    = 0;
			$incomplete = 0;

			foreach ($this->_manifest->elements as $elementId => $element)
			{
				if (!isset($status->elements))
				{
					$status->elements = new stdClass();
				}
				$status->elements->$elementId = self::getElementStatus($element, $pub);
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
	public function getElementStatus($element, $pub = null)
	{
		// Get block element model
		$elModel = new \Components\Publications\Models\BlockElements($this->_parent->_db);

		$status = $elModel->getStatus($element->type, $element, $pub);
		return $status;
	}

	/**
	 * Get default manifest for the block element
	 *
	 * @return  void
	 */
	public function getElementManifest()
	{
		$manifest = array (
			'name' 		=> 'metadata',
			'type' 		=> 'metadata',
			'label'		=> 'Metadata',
			'about'		=> '<p>Please fill out metadata</p>',
			'adminTips'	=> '',
			'params' 	=> array (
				'required' 		=> 0,
				'aliasmap' 		=> 'meta',
				'field' 		=> 'metadata',
				'input' 		=> 'editor',
				'placeholder'	=> 'Type text',
				'default'		=> '',
				'maxlength' 	=> '3000',
				'cols'			=> '50',
				'rows'			=> '6'
			)
		);
		return json_decode(json_encode($manifest), false);
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
				'name' 			=> 'description',
				'label' 		=> 'Description',
				'title' 		=> 'Publication Description',
				'draftHeading' 	=> 'Name and describe your publication',
				'draftTagline'	=> 'Here is what\'s required:',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 	=> array(
					1 => array (
						'name' 		=> 'metadata',
						'type' 		=> 'metadata',
						'label'		=> 'Publication Title',
						'about'		=> '<p>Pick a descriptive yet concise publication title that will quickly tell users about its content.</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'required' 		=> 1,
							'aliasmap' 		=> 'title',
							'field' 		=> 'title',
							'input' 		=> 'text',
							'placeholder'	=> 'Type publication title',
							'default'		=> 'Untitled Draft',
							'maxlength' 	=> '255'
						)
					),
					2 => array (
						'name' 		=> 'metadata',
						'type' 		=> 'metadata',
						'label'		=> 'Publication Abstract',
						'about'		=> '<p>Provide a short (max 255 characters) abstract for your publication</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'required' 		=> 1,
							'aliasmap' 		=> 'abstract',
							'field' 		=> 'abstract',
							'input' 		=> 'textarea',
							'placeholder'	=> 'Type publication abstract',
							'default'		=> '',
							'maxlength' 	=> '255',
							'cols'			=> '50',
							'rows'			=> '3'
						)
					),
					3 => array (
						'name' 		=> 'metadata',
						'type' 		=> 'metadata',
						'label'		=> 'Publication Description',
						'about'		=> '<p>Describe your publication in detail</p>',
						'adminTips'	=> '',
						'params' 	=> array (
							'required' 		=> 1,
							'aliasmap' 		=> 'description',
							'field' 		=> 'description',
							'input' 		=> 'editor',
							'placeholder'	=> 'Describe publication',
							'default'		=> '',
							'maxlength' 	=> '3000',
							'cols'			=> '50',
							'rows'			=> '6'
						)
					)
				),
				'params' => array('required' => 1, 'published_editing' => 0, 'collapse_elements' => 1)
			);

			if ($new == true)
			{
				$manifest['label']        = 'Metadata';
				$manifest['title']        = 'Publication Metadata';
				$manifest['draftHeading'] = 'Provide some metadata';
				$manifest['elements']     = array(1 => $this->getElementManifest());
			}

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
