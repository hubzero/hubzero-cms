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
 * Content block
 */
class PublicationsBlockContent extends PublicationsModelBlock
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
	* Step number
	*
	* @var		integer
	*/
	protected	$_sequence = 0;

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

		// Get extra params
		$params 	 = $this->_manifest->params;
		$useHanlders = isset($params->use_hanlders) && $params->use_hanlders == 1 ? true : false;

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

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

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
	 * Save block content
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

		// Make sure we have current attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

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
					// Record update time
					$data 				= new stdClass;
					$data->updated 		= JFactory::getDate()->toSql();
					$data->updated_by 	= $actor;
					$pub->_curationModel->saveUpdate($data, $id, $this->_name, $pub, $sequence);
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
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

		// Transfer data of each element
		foreach ($manifest->elements as $elementId => $element)
		{
			$attModel->transferData($element->params->type, $element,
				$elementId, $pub, $this->_manifest->params,
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
		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'selector.css');
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS
			. 'css' . DS . 'selector.css');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'links');

		// Get block element model
		$elModel = new PublicationsModelBlockElements($this->_parent->_db);

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
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Start status
		$status 	 = new PublicationsModelStatus();

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
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

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
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

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
	public function saveItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : JRequest::getInt( 'aid', 0 );

		// Load attachment
		$row = new PublicationAttachment( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid))
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
			return false;
		}

		// Attachment type
		$type = $row->type;

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

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
	public function deleteItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $aid = 0)
	{
		$aid = $aid ? $aid : JRequest::getInt( 'aid', 0 );

		// Load attachment
		$row = new PublicationAttachment( $this->_parent->_db );

		// We need attachment record
		if (!$aid || !$row->load($aid))
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ERROR_EDIT_CONTENT'));
			return false;
		}

		// Attachment type
		$type = $row->type;

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

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
		$obj = new PublicationBlock($this->_parent->_db);
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