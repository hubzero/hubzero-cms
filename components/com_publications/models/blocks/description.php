<?php
/**
 * @package		HUBzero CMS
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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

// Check to ensure this file is within the rest of the framework
defined('_JEXEC') or die('Restricted access');

/**
 * Description block
 */
class PublicationsBlockDescription extends PublicationsModelBlock
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name 			= 'description';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected	$_parentname 	= 'description';

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected	$_manifest 		= NULL;

	/**
	* Step number
	*
	* @var		integer
	*/
	protected	$_sequence 		= 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = NULL, $manifest = NULL, $viewname = 'edit', $sequence = 0)
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Register sequence
		$this->_sequence	= $sequence;

		if ($viewname == 'curator')
		{
			// Output HTML
			$view = new JView( array('name'=>'curation', 'layout'=> 'block' ) );
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

		// Build url
		$route = $pub->_project->provisioned
					? 'index.php?option=com_publications&task=submit'
					: 'index.php?option=com_projects&alias='
						. $pub->_project->alias . '&active=publications';

		$pub->url = JRoute::_($route . '&pid=' . $pub->id . '&section='
			. $this->_name . '&step=' . $sequence . '&move=continue');

		// Get block status
		$status = self::getStatus($pub);

		// Get block status review
		$status->review = $pub->_curationModel->_progress->blocks->$sequence->review;

		// Get block element model
		$elModel = new PublicationsModelBlockElements($this->_parent->_db);

		// Properties object
		$master 			= new stdClass;
		$master->block 		= $this->_name;
		$master->sequence 	= $this->_sequence;
		$master->params		= $this->_manifest->params;
		$master->props		= $elModel->getActiveElement($status->elements, $status->review);

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent( $pub, $viewname, $status, $master );
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $sequence;
		$view->showControls	= 1;
		$view->status		= $status;
		$view->master		= $master;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block
	 *
	 * @return  string  HTML
	 */
	public function save( $manifest = NULL, $sequence = 0, $pub = NULL, $actor = 0, $elementId = 0)
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

		// Load publication version
		$row = new PublicationVersion( $this->_parent->_db );

		if (!$row->load($pub->version_id))
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'));
			return false;
		}

		// Track changes
		$changed = 0;
		$missed  = 0;

		// Incoming
		$nbtags = JRequest::getVar( 'nbtag', array(), 'request', 'array' );

		// Parse metadata
		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $pub->metadata, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = PublicationsHtml::_txtUnpee($match[2]);
			}
		}

		// Save each element
		foreach ($this->_manifest->elements as $id => $element)
		{
			// Are we saving just one element?
			if ($elementId && $id != $elementId)
			{
				continue;
			}

			$field 	  = $element->params->field;
			$aliasmap = $element->params->aliasmap;
			$input 	  = $element->params->input;
			$required = $element->params->required;

			if ($field == 'metadata')
			{
				$value = isset($nbtags[$aliasmap]) ? trim(stripslashes($nbtags[$aliasmap])) : NULL;

				if (!$value && $required)
				{
					$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
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
				$value = trim(JRequest::getVar( $field, '', 'post' ));
				$value = ($input == 'editor')
					? stripslashes($value)
					: \Hubzero\Utility\Sanitize::clean($value);

				if (!$value && $required)
				{
					$missed++;
				}
				else
				{
					if ($row->$field != $value)
					{
						$changed++;

						// Record update time
						$data 				= new stdClass;
						$data->updated 		= JFactory::getDate()->toSql();
						$data->updated_by 	= $actor;
						$pub->_curationModel->saveUpdate($data, $id, $this->_name, $pub, $sequence);
					}
					$row->$field = $value;
				}
			}
		}

		// Update modified info
		if ($changed)
		{
			$row->modified 	  = JFactory::getDate()->toSql();
			$row->modified_by = $actor;
			$this->_parent->set('_update', 1);
		}

		// Report error
		if ($missed && $this->_manifest->params->collapse_elements == 0)
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_MISSING_REQUIRED'));
		}

		// Save
		if (!$row->store())
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_SAVE_PUBLICATION'));
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
	public function buildContent( $pub = NULL, $viewname = 'edit', $status, $master )
	{
		// Get block element model
		$elModel = new PublicationsModelBlockElements($this->_parent->_db);

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
	public function getStatus( $pub = NULL, $manifest = NULL, $elementId = NULL )
	{
		// Set block manifest
		if ($this->_manifest === NULL)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		// Start status
		$status 	 = new PublicationsModelStatus();

		// Return element status
		if ($elementId !== NULL && isset($this->_manifest->elements->$elementId))
		{
			return self::getElementStatus($this->_manifest->elements->$elementId, $pub);
		}

		// Check against manifested requirements
		if ($this->_manifest && $this->_manifest->elements)
		{
			// Check if requirements are satisfied for each attachment element
			$i 		 	= 0;
			$success 	= 0;
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
	public function getElementStatus( $element, $pub = NULL )
	{
		// Get block element model
		$elModel = new PublicationsModelBlockElements($this->_parent->_db );

		$status = $elModel->getStatus( $element->type, $element, $pub );
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
		$obj = new PublicationBlock($this->_parent->_db);
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
				'params'	=> array( 'required' => 1, 'published_editing' => 0, 'collapse_elements' => 1 )
			);

			if ($new == true)
			{
				$manifest['label']			= 'Metadata';
				$manifest['title']			= 'Publication Metadata';
				$manifest['draftHeading'] 	= 'Provide some metadata';
				$manifest['elements'] 		= array(1 => $this->getElementManifest());
			}

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}