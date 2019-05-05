<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

/**
 * Review block
 */
class Review extends Base
{
	/**
  * Element name
  *
  * @var		string
  */
	protected	$_name 			= 'review';

	/**
  * Parent block name
  *
  * @var		string
  */
	protected	$_parentname 	= null;

	/**
  * Default manifest
  *
  * @var		string
  */
	protected	$_manifest 		= null;

	/**
  * Numeric block ID
  *
  * @var		integer
  */
	protected	$_blockId 		= 0;

	/**
	 * Display block content
	 *
	 * @return  string  HTML
	 */
	public function display( $pub = null, $manifest = null, $viewname = 'review', $blockId = 0)
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
			// Do not show
			return;
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
		$view->showControls	= 0;

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
	public function buildContent( $pub = null, $viewname = 'edit' )
	{
		$name = $viewname == 'freeze' || $viewname == 'curator' ? 'freeze' : 'draft';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> $name,
				'layout'	=> 'review'
			)
		);

		\Hubzero\Document\Assets::addComponentStylesheet('com_projects', 'css/calendar');

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
	public function save( $manifest = null, $blockId = 0, $pub = null, $actor = 0, $elementId = 0)
	{
		// Set block manifest
		if ($this->_manifest === null)
		{
			$this->_manifest = $manifest ? $manifest : self::getManifest();
		}

		return true;
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $pub = null, $manifest = null, $elementId = null )
	{
		// Start status
		$status 	 	= new \Components\Publications\Models\Status();
		$status->status = 1;
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
				'name' 			=> 'review',
				'label' 		=> 'Review',
				'title' 		=> 'Publication Review',
				'draftHeading' 	=> 'Review Publication',
				'draftTagline'	=> 'Here is your publication at a glance:',
				'about'			=> '',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array(  'required' => 1, 'published_editing' => 0 )
			);

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
