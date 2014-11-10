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

include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_citations' . DS . 'tables' . DS . 'citation.php' );
include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'
	. DS . 'com_citations' . DS . 'tables' . DS . 'association.php' );
include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations'
	. DS . 'helpers' . DS . 'format.php' );

/**
 * Citations block
 */
class PublicationsBlockCitations extends PublicationsModelBlock
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name = 'citations';

	/**
	* Parent block name
	*
	* @var		string
	*/
	protected	$_parentname 	= NULL;

	/**
	* Default manifest
	*
	* @var		string
	*/
	protected	$_manifest 	= NULL;

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

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent( $pub, $viewname );
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $sequence;
		$view->showControls	= 2;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Build panel content
	 *
	 * @return  string  HTML
	 */
	public function buildContent( $pub = NULL, $viewname = 'edit' )
	{
		$name = $viewname == 'freeze' || $viewname == 'curator' ? 'freeze' : 'draft';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> $name,
				'layout'	=> 'citations'
			)
		);

		// Get selector styles
		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'files' . DS . 'css' . DS . 'selector.css');
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS
			. 'css' . DS . 'selector.css');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'links');

		if (!isset($pub->_citations))
		{
			$config = JComponentHelper::getParams( 'com_publications' );
			$pub->_citationFormat = $config->get('citation_format', 'apa');

			// Get citations for this publication
			$c = new CitationsCitation( $this->_parent->_db );
			$pub->_citations = $c->getCitations( 'publication', $pub->id );
		}

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_sequence;

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

		// Load publication version
		$objP = new Publication( $this->_parent->_db );

		if (!$objP->load($pub->id))
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			return false;
		}

		if (!isset($pub->_citations))
		{
			$config = JComponentHelper::getParams( 'com_publications' );
			$pub->_citationFormat = $config->get('citation_format', 'apa');

			// Get citations for this publication
			$c = new CitationsCitation( $this->_parent->_db );
			$pub->_citations = $c->getCitations( 'publication', $pub->id );
		}

		// Incoming
		$url = JRequest::getVar('citation-doi', '');
		if (!$url)
		{
			return true;
		}

		$parts 	= explode("doi:", $url);
		$doi   	= count($parts) > 1 ? $parts[1] : $url;

		// Get links plugin
		JPluginHelper::importPlugin( 'projects', 'links' );
		$dispatcher = JDispatcher::getInstance();

		// Plugin params
		$plugin_params = array(
			$pub->id,
			$doi,
			$pub->_citationFormat,
			$actor,
			true
		);

		// Attach citation
		$output = $dispatcher->trigger( 'attachCitation', $plugin_params);

		if (isset($output[0]))
		{
			if ($output[0]['success'])
			{
				$this->set('_message', JText::_('PLG_PROJECTS_PUBLICATIONS_CITATION_SAVED'));

				// Reflect the update in curation record
				$this->_parent->set('_update', 1);
			}
			else
			{
				$this->setError($output[0]['error']);
				return false;
			}
		}
		else
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_CITATION_ERROR_SAVING'));
			return false;
		}

		return true;
	}

	/**
	 * Add new citation
	 *
	 * @return  void
	 */
	public function addItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cite = JRequest::getVar('cite', array(), 'post', 'none', 2);

		$new  = $cite['id'] ? false : true;

		if (!$cite['type'] || !$cite['title'])
		{
			$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		$citation = new CitationsCitation( $this->_parent->_db );
		if (!$citation->bind($cite))
		{
			$this->setError($citation->getError());
			return false;
		}

		$citation->created 		= $new ? JFactory::getDate()->toSql() : $citation->created;
		$citation->uid			= $new ? $actor : $citation->uid;
		$citation->published	= 1;

		if (!$citation->store(true))
		{
			// This really shouldn't happen.
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_SAVE'));
			return false;
		}

		// Create association
		if ($new == true && $citation->id)
		{
			$assoc 		 = new CitationsAssociation( $this->_parent->_db );
			$assoc->oid  = $pub->id;
			$assoc->tbl  = 'publication';
			$assoc->type = 'owner';
			$assoc->cid  = $citation->id;

			// Store new content
			if (!$assoc->store())
			{
				$this->setError($assoc->getError());
				return false;
			}
		}

		$this->set('_message', JText::_('PLG_PROJECTS_PUBLICATIONS_CITATIONS_SUCCESS_SAVE') );
		return true;
	}

	/**
	 * Update citation record
	 *
	 * @return  void
	 */
	public function saveItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$this->addItem($manifest, $sequence, $pub, $actor, $elementId, $cid);
		return;
	}

	/**
	 * Delete citation
	 *
	 * @return  void
	 */
	public function deleteItem ($manifest, $sequence, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cid = $cid ? $cid : JRequest::getInt( 'cid', 0 );

		// Get links plugin
		JPluginHelper::importPlugin( 'projects', 'links' );
		$dispatcher = JDispatcher::getInstance();

		// Plugin params
		$plugin_params = array(
			$pub->id,
			$cid,
			true
		);

		// Attach citation
		$output = $dispatcher->trigger( 'unattachCitation', $plugin_params);

		if (isset($output[0]))
		{
			if ($output[0]['success'])
			{
				$this->set('_message', JText::_('PLG_PROJECTS_PUBLICATIONS_CITATION_DELETED'));

				// Reflect the update in curation record
				$this->_parent->set('_update', 1);
			}
			else
			{
				$this->setError($output[0]['error']);
				return false;
			}
		}
		else
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_CITATION_ERROR_SAVING'));
			return false;
		}

		return true;

	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $pub = NULL, $manifest = NULL, $elementId = NULL )
	{
		$status 	 = new PublicationsModelStatus();

		if (!isset($pub->_citations))
		{
			$config = JComponentHelper::getParams( 'com_publications' );
			$pub->_citationFormat = $config->get('citation_format', 'apa');

			// Get citations for this publication
			$c = new CitationsCitation( $this->_parent->_db );
			$pub->_citations = $c->getCitations( 'publication', $pub->id );
		}

		// Required?
		$required = $manifest->params->required;
		$status->status = $required && (!$pub->_citations || count($pub->_citations) == 0) ? 0 : 1;
		$status->status = !$required && (!$pub->_citations || count($pub->_citations) == 0) ? 2 : $status->status;

		return $status;
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
				'name' 			=> 'citations',
				'label' 		=> 'Citations',
				'title' 		=> 'Citations to integral or companion resources',
				'draftHeading' 	=> 'Add citations',
				'draftTagline'	=> 'Cite integral or companion resources',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array( 'required' => 0, 'published_editing' => 1 )
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}