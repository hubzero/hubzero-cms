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
 * Tags block
 */
class PublicationsBlockTags extends PublicationsModelBlock
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name = 'tags';

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
				'layout'	=> 'tags'
			)
		);

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_sequence;

		// Get tags
		$tagsHelper = new PublicationTags( $this->_parent->_db);

		$tags_men = $tagsHelper->get_tags_on_object($pub->id, 0, 0, 0, 0);

		$mytagarray = array();
		foreach ($tags_men as $tag_men)
		{
			$mytagarray[] = $tag_men['raw_tag'];
		}
		$view->tags = implode(', ', $mytagarray);

		// Get categories
		$rt = new PublicationCategory( $this->_parent->_db );
		$view->categories = $rt->getContribCategories();

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

		$tagsHelper = new PublicationTags( $this->_parent->_db );
		$tags 		= trim(JRequest::getVar('tags', '', 'post'));
		$tagsHelper->tag_object($actor, $pub->id, $tags, 1);

		// Reflect the update in curation record
		$this->_parent->set('_update', 1);

		// Save category
		$cat = JRequest::getInt( 'pubtype', 0 );
		if ($cat && $objP->_category->id != $cat)
		{
			$objP->category = $cat;
			$objP->store();
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
		// Start status
		$status 	 = new PublicationsModelStatus();

		$tagsHelper 	= new PublicationTags( $this->_parent->_db);
		$status->status = $tagsHelper->countTags($pub->id) > 0 ? 1 : 0;

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
				'name' 			=> 'tags',
				'label' 		=> 'Tags',
				'title' 		=> 'Publication Tags',
				'draftHeading' 	=> 'Add tags',
				'draftTagline'	=> 'Make your publication more discoverable:',
				'about'			=> 'Tags help users find your publication. Before adding your own tags, try finding good matches in existing tag library.',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array( 'required' => 1, 'published_editing' => 0 )
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}