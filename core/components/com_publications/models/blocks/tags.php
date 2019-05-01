<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'tags.php';

/**
 * Tags block
 */
class Tags extends Base
{
	/**
	 * Block name
	 *
	 * @var		string
	 */
	protected $_name = 'tags';

	/**
	 * Parent block name
	 *
	 * @var		string
	 */
	protected $_parentname = null;

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

		$view->manifest 	= $this->_manifest;
		$view->content 		= self::buildContent($pub, $viewname);
		$view->pub			= $pub;
		$view->active		= $this->_name;
		$view->step			= $blockId;
		$view->showControls	= 4;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Build panel content
	 *
	 * @return  string  HTML
	 */
	public function buildContent($pub = null, $viewname = 'edit')
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
		$view->step		= $this->_blockId;

		// Get categories
		$view->categories = $pub->category()->getContribCategories();

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block content
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
		$objP = new \Components\Publications\Tables\Publication($this->_parent->_db);

		if (!$objP->load($pub->id))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
			return false;
		}

		$tagsHelper = new \Components\Publications\Helpers\Tags($this->_parent->_db);
		$tags = trim(Request::getString('tags', '', 'post'));
		$tagsHelper->tag_object($actor, $pub->id, $tags, 1);

		// Reflect the update in curation record
		$this->_parent->set('_update', 1);

		// Save category
		$cat = Request::getInt('pubtype', 0);
		if ($cat && $pub->_category->id != $cat)
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
	public function getStatus($pub = null, $manifest = null, $elementId = null)
	{
		// Start status
		$status = new \Components\Publications\Models\Status();

		$tagsHelper = new \Components\Publications\Helpers\Tags($this->_parent->_db);

		// Required?
		$required = $manifest->params->required;
		$count = $tagsHelper->countTags($pub->id);
		$status->status = $required && $count == 0 ? 0 : 1;
		$status->status = !$required && $count == 0 ? 2 : $status->status;

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
				'name' 			=> 'tags',
				'label' 		=> 'Tags',
				'title' 		=> 'Publication Tags',
				'draftHeading' 	=> 'Add tags',
				'draftTagline'	=> 'Make your publication more discoverable:',
				'about'			=> 'Tags help users find your publication. Before adding your own tags, try finding good matches in existing tag library.',
				'adminTips'		=> '',
				'elements' 		=> array(),
				'params'		=> array('required' => 1, 'published_editing' => 0)
			);

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
