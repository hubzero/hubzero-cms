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

include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
include_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

/**
 * Citations block
 */
class Citations extends Base
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

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent( $pub, $viewname );
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $blockId;
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
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'links');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files','selector');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications','selector');

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$c = new \Components\Citations\Tables\Citation( $this->_parent->_db );
			$pub->_citations = $c->getCitations( 'publication', $pub->id );
		}

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_blockId;

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

		// Load publication version
		$objP = new \Components\Publications\Tables\Publication( $this->_parent->_db );

		if (!$objP->load($pub->id))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			return false;
		}

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$c = new \Components\Citations\Tables\Citation( $this->_parent->_db );
			$pub->_citations = $c->getCitations( 'publication', $pub->id );
		}

		// Incoming
		$url = Request::getVar('citation-doi', '');
		if (!$url)
		{
			return true;
		}

		$parts 	= explode("doi:", $url);
		$doi   	= count($parts) > 1 ? $parts[1] : $url;

		$citationFormat = $pub->config('citation_format', 'apa');

		// Plugin params
		$plugin_params = array(
			$pub->id,
			$doi,
			$citationFormat,
			$actor,
			true
		);

		// Attach citation
		$output = Event::trigger( 'projects.attachCitation', $plugin_params);

		if (isset($output[0]))
		{
			if ($output[0]['success'])
			{
				$this->set('_message', Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATION_SAVED'));

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
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATION_ERROR_SAVING'));
			return false;
		}

		return true;
	}

	/**
	 * Add new citation
	 *
	 * @return  void
	 */
	public function addItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cite = Request::getVar('cite', array(), 'post', 'none', 2);

		$new  = $cite['id'] ? false : true;

		if (!$cite['type'] || !$cite['title'])
		{
			$this->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}

		$citation = new \Components\Citations\Tables\Citation( $this->_parent->_db );
		if (!$citation->bind($cite))
		{
			$this->setError($citation->getError());
			return false;
		}

		$citation->created 		= $new ? Date::toSql() : $citation->created;
		$citation->uid			= $new ? $actor : $citation->uid;
		$citation->published	= 1;

		if (!$citation->store(true))
		{
			// This really shouldn't happen.
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_SAVE'));
			return false;
		}

		// Create association
		if ($new == true && $citation->id)
		{
			$assoc 		 = new \Components\Citations\Tables\Association( $this->_parent->_db );
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

		$this->set('_message', Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_SUCCESS_SAVE') );
		return true;
	}

	/**
	 * Update citation record
	 *
	 * @return  void
	 */
	public function saveItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$this->addItem($manifest, $blockId, $pub, $actor, $elementId, $cid);
		return;
	}

	/**
	 * Delete citation
	 *
	 * @return  void
	 */
	public function deleteItem ($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cid = $cid ? $cid : Request::getInt( 'cid', 0 );

		// Plugin params
		$plugin_params = array(
			$pub->id,
			$cid,
			true
		);

		// Attach citation
		$output = Event::trigger( 'projects.unattachCitation', $plugin_params);

		if (isset($output[0]))
		{
			if ($output[0]['success'])
			{
				$this->set('_message', Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATION_DELETED'));

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
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATION_ERROR_SAVING'));
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
		$status 	 = new \Components\Publications\Models\Status();

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$c = new \Components\Citations\Tables\Citation( $this->_parent->_db );
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
		$obj = new \Components\Publications\Tables\Block($this->_parent->_db);
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