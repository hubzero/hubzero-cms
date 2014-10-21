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
 * License block
 */
class PublicationsBlockLicense extends PublicationsModelBlock
{
	/**
	* Block name
	*
	* @var		string
	*/
	protected	$_name 			= 'license';

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

		// Get selector styles
		$document = JFactory::getDocument();
		$document->addStyleSheet('plugins' . DS . 'projects' . DS . 'publications' . DS . 'css' . DS . 'selector.css');

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> $name,
				'layout'	=> 'license'
			)
		);

		$view->pub		= $pub;
		$view->manifest = $this->_manifest;
		$view->step		= $this->_sequence;

		$objL = new PublicationLicense( $this->_parent->_db );

		// Get selected license
		$view->license = $objL->getPubLicense( $pub->version_id );

		$view->selections = $objL->getBlockLicenses( $this->_manifest, $view->license );

		// Pre-select single available license
		if (!$view->license && count($view->selections) == 1)
		{
			$view->license = new PublicationLicense( $this->_parent->_db );;
			$view->license->load($view->selections[0]->id);
		}

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
		$row = new PublicationVersion( $this->_parent->_db );

		if (!$row->load($pub->version_id))
		{
			$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'));
			return false;
		}

		$originalType = $row->license_type;
		$originalText = $row->license_text;

		// Load license class
		$objL = new PublicationLicense( $this->_parent->_db );

		// Incoming - license screen agreements
		$license = JRequest::getInt( 'license', 0, 'post' );
		$text 	 = \Hubzero\Utility\Sanitize::clean(JRequest::getVar( 'license_text', '', 'post'));
		$agree 	 = JRequest::getInt( 'agree', 0, 'post');
		$custom  = JRequest::getVar( 'substitute', array(), 'request', 'array' );

		if ($license)
		{
			if (!$objL->load($license))
			{
				$this->setError( JText::_('There was a problem saving license selection') );
				return false;
			}

			if ($objL->agreement == 1 && !$agree)
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_AGREEMENT') );
				return false;
			}
			elseif ($objL->customizable == 1 && !$text)
			{
				$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_TEXT') );
				return false;
			}

			$row->license_type = $license;
			$text = preg_replace("/\r/", '', $text);
			$row->license_text = $text;

			// Pre-defined license text
			if ($objL->text && $objL->customizable == 0)
			{
				$row->license_text = $objL->text;

				// Do we have template items to replace?
				preg_match_all('/\[([^\]]*)\]/', $objL->text, $substitutes);
				if (count($substitutes) > 1)
				{
					foreach ($substitutes[1] as $sub)
					{
						if (!isset($custom[$sub]) || !$custom[$sub])
						{
							$this->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_CUSTOM') );
							return false;
						}
						else
						{
							$row->license_text = preg_replace('/\[' . $sub . '\]/', trim($custom[$sub]), $row->license_text);
						}
					}
				}
			}

			$row->store();

			// Save agreement
			$row->saveParam($pub->version_id, 'licenseagreement', 1);

			// Save custom fields in version params
			foreach ($custom as $label => $value)
			{
				$row->saveParam($pub->version_id, 'licensecustom' . strtolower($label), trim($value));
			}

			if ($license != $originalType || $text != $originalText)
			{
				$this->_parent->set('_update', 1);
			}

			// Check agreements
			return true;
		}

		// Incoming - selector screen
		$selections = JRequest::getVar( 'selecteditems', '');
		$toAttach = explode(',', $selections);

		$i = 0;
		foreach ($toAttach as $license)
		{
			if (!trim($license))
			{
				continue;
			}

			// Make sure license exists
			if ($objL->load($license))
			{
				$row->license_type = $license;
				$i++;
				$row->store();

				// Clear agreement if license is changed
				if ($originalType != $license)
				{
					// Save agreement
					$row->saveParam($pub->version_id, 'licenseagreement', 0);
					//$this->_parent->set('_update', 1);
				}

				// Only one choice
				break;
			}
		}

		if ($i)
		{
			$this->set('_message', JText::_('License selection saved') );
			return true;
		}
		else
		{
			$this->setError( JText::_('There was a problem saving license selection') );
			return false;
		}
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

		// Get version params
		$pubParams = new JParameter( $pub->params );

		$status->status = 1;

		// Load license class
		$objL = new PublicationLicense( $this->_parent->_db );

		if ($pub->license_type && $objL->load($pub->license_type))
		{
			$agreement = $pubParams->get('licenseagreement');

			// Missing agreement?
			if ($objL->agreement == 1 && !$agreement)
			{
				$status->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_AGREEMENT') );
				$status->status = 0;
			}

			if ($objL->customizable == 1
				&& $objL->text && !$pub->license_text)
			{
				$status->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_LICENSE_NEED_TEXT') );
				$status->status = 0;
			}

			if ($pub->license_text)
			{
				preg_replace('/\[([^]]+)\]/', ' ', $pub->license_text, -1, $bingo);
				if ($bingo)
				{
					$status->setError( JText::_('Default values need to be substituted') );
					$status->status = 0;
				}
			}
		}
		else
		{
			$status->status = 0;
		}

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
				'name' 			=> 'license',
				'label' 		=> 'License',
				'title' 		=> 'Publication License',
				'draftHeading' 	=> 'Choose License',
				'draftTagline'	=> 'Define copyright and terms of use:',
				'about'			=> 'It is important that you provide a license for your publication stating your copyright and terms of use of your content.',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array( 'required' => 1, 'published_editing' => 0, 'include' => array(), 'exclude' => array())
			);

			return json_decode(json_encode($manifest), FALSE);
		}

		return $manifest;
	}
}