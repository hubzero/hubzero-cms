<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Block;

use Components\Publications\Models\Block as Base;
use stdClass;

include_once \Component::path('com_citations') . DS . 'models' . DS . 'citation.php';
include_once \Component::path('com_citations') . DS . 'helpers' . DS . 'format.php';

/**
 * Citations block
 */
class Citations extends Base
{
	/**
	 * Block name
	 *
	 * @var  string
	 */
	protected $_name = 'citations';

	/**
	 * Parent block name
	 *
	 * @var  string
	 */
	protected $_parentname = null;

	/**
	 * Default manifest
	 *
	 * @var  string
	 */
	protected $_manifest = null;

	/**
	 * Numeric block ID
	 *
	 * @var  integer
	 */
	protected $_blockId = 0;

	/**
	 * Display block content
	 *
	 * @param   object   $pub
	 * @param   object   $manifest
	 * @param   string   $viewname
	 * @param   itneger  $blockId
	 * @return  string   HTML
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
					'name'   => 'curation',
					'layout' => 'block'
				)
			);
		}
		else
		{
			$name = $viewname == 'freeze' ? 'freeze' : 'draft';

			// Output HTML
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'projects',
					'element' => 'publications',
					'name'    => $name,
					'layout'  => 'wrapper'
				)
			);
		}

		$view->manifest     = $this->_manifest;
		$view->content      = self::buildContent($pub, $viewname);
		$view->pub          = $pub;
		$view->active       = $this->_name;
		$view->step         = $blockId;
		$view->showControls = 2;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Build panel content
	 *
	 * @param   object  $pub
	 * @param   string  $viewname
	 * @return  string  HTML
	 */
	public function buildContent($pub = null, $viewname = 'edit')
	{
		$name = $viewname == 'freeze' || $viewname == 'curator' ? 'freeze' : 'draft';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'publications',
				'name'    => $name,
				'layout'  => 'citations'
			)
		);

		// Get selector styles
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'links');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'files', 'selector');
		\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'selector');

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$cc = \Components\Citations\Models\Citation::all();

			$a = \Components\Citations\Models\Association::blank()->getTableName();
			$c = $cc->getTableName();

			$pub->_citations = $cc
				->join($a, $a . '.cid', $c . '.id', 'inner')
				->whereEquals($c . '.published', 1)
				->whereEquals($a . '.tbl', 'publication')
				->whereEquals($a . '.oid', $pub->id)
				->order($c . '.affiliated', 'asc')
				->order($c . '.year', 'desc')
				->rows();
		}

		$view->pub      = $pub;
		$view->manifest = $this->_manifest;
		$view->step     = $this->_blockId;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Save block content
	 *
	 * @param   object   $manifest
	 * @param   integer  $blockId
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @return  string   HTML
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

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$cc = \Components\Citations\Models\Citation::all();

			$a = \Components\Citations\Models\Association::blank()->getTableName();
			$c = $cc->getTableName();

			$pub->_citations = $cc
				->join($a, $a . '.cid', $c . '.id', 'inner')
				->whereEquals($c . '.published', 1)
				->whereEquals($a . '.tbl', 'publication')
				->whereEquals($a . '.oid', $pub->id)
				->order($c . '.affiliated', 'asc')
				->order($c . '.year', 'desc')
				->rows();
		}

		// Incoming
		$url = Request::getString('citation-doi', '');
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
		$output = Event::trigger('projects.attachCitation', $plugin_params);

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
	 * @param   object   $manifest
	 * @param   integer  $blockId
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   integer  $cid
	 * @return  void
	 */
	public function addItem($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cite = Request::getArray('cite', array(), 'post');

		$new  = $cite['id'] ? false : true;

		if (!$cite['type'] || !$cite['title'])
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_MISSING_REQUIRED'));
			return false;
		}
		unset($cite['uri']);
		$citation = \Components\Citations\Models\Citation::all()->set($cite);
		$citation->set('created', $new ? Date::toSql() : $citation->get('created'));
		$citation->set('uid', $new ? $actor : $citation->get('uid'));
		$citation->set('published', 1);

		if (!$citation->save())
		{
			// This really shouldn't happen.
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_SAVE'));
			return false;
		}

		// Create association
		if ($new == true && $citation->get('id'))
		{
			$assoc = \Components\Citations\Models\Association::blank();
			$assoc->set('oid', $pub->id);
			$assoc->set('tbl', 'publication');
			$assoc->set('type', 'owner');
			$assoc->set('cid', $citation->get('id'));

			// Store new content
			if (!$assoc->save())
			{
				$this->setError($assoc->getError());
				return false;
			}
		}
		$this->set('_message', Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_SUCCESS_SAVE') );
		$this->_parent->set('_update', 1);
		return true;
	}

	/**
	 * Update citation record
	 *
	 * @param   object   $manifest
	 * @param   integer  $blockId
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   integer  $cid
	 * @return  void
	 */
	public function saveItem($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$this->addItem($manifest, $blockId, $pub, $actor, $elementId, $cid);
		return;
	}

	/**
	 * Delete citation
	 *
	 * @param   object   $manifest
	 * @param   integer  $blockId
	 * @param   object   $pub
	 * @param   integer  $actor
	 * @param   integer  $elementId
	 * @param   integer  $cid
	 * @return  void
	 */
	public function deleteItem($manifest, $blockId, $pub, $actor = 0, $elementId = 0, $cid = 0)
	{
		$cid = $cid ? $cid : Request::getInt('cid', 0);

		// Plugin params
		$plugin_params = array(
			$pub->id,
			$cid,
			true
		);

		// Attach citation
		$output = Event::trigger('projects.unattachCitation', $plugin_params);

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

		$this->_parent->set('_update', 1);
		return true;

	}

	/**
	 * Check completion status
	 *
	 * @param   object   $pub
	 * @param   object   $manifest
	 * @param   integer  $elementId
	 * @return  object
	 */
	public function getStatus($pub = null, $manifest = null, $elementId = null)
	{
		$status = new \Components\Publications\Models\Status();

		if (!isset($pub->_citations))
		{
			// Get citations for this publication
			$cc = \Components\Citations\Models\Citation::all();

			$a = \Components\Citations\Models\Association::blank()->getTableName();
			$c = $cc->getTableName();

			$pub->_citations = $cc
				->join($a, $a . '.cid', $c . '.id', 'inner')
				->whereEquals($c . '.published', 1)
				->whereEquals($a . '.tbl', 'publication')
				->whereEquals($a . '.oid', $pub->id)
				->order($c . '.affiliated', 'asc')
				->order($c . '.year', 'desc')
				->rows();
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
	 * @param   bool  $new
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
				'name'         => 'citations',
				'label'        => 'Citations',
				'title'        => 'Citations to integral or companion resources',
				'draftHeading' => 'Add citations',
				'draftTagline' => 'Cite integral or companion resources',
				'about'        => '',
				'adminTips'    => '',
				'elements'     => array(),
				'params'       => array('required' => 0, 'published_editing' => 1)
			);

			return json_decode(json_encode($manifest), false);
		}

		return $manifest;
	}
}
