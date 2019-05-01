<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\BlockElement;

use Components\Publications\Models\BlockElement as Base;

/**
 * Renders URL selector element
 */
class Dataselector extends Base
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'dataselector';

	/**
	 * Git helper
	 *
	 * @var  string
	 */
	protected $_git = null;

	/**
	 * Project repo path
	 *
	 * @var  string
	 */
	protected $path = null;

	/**
	 * Render
	 *
	 * @param   integer  $elementid
	 * @param   object   $manifest
	 * @param   object   $pub
	 * @param   string   $viewname
	 * @param   string   $status
	 * @param   object   $master
	 * @param   integer  $order
	 * @return  object
	 */
	public function render($elementid, $manifest, $pub = null, $viewname = 'edit', $status = null, $master = null, $order = 0)
	{
		$html = '';

		// Get project path
		$this->path = $pub->_project->repo()->get('path');

		$showElement = $master->props['showElement'];
		$total       = $master->props['total'];

		// Incoming
		$activeElement = Request::getInt('el', $showElement);

		// Git helper
		if (!$this->_git)
		{
			include_once \Component::path('com_projects') . DS . 'helpers' . DS . 'githelper.php';
			$this->_git = new \Components\Projects\Helpers\Git($this->path);
		}

		// Do we need to collapse inactive elements?
		$collapse = isset($master->params->collapse_elements) && $master->params->collapse_elements ? 1 : 0;

		switch ($viewname)
		{
			case 'edit':
			default:
				$html = $this->drawSelector(
					$elementid,
					$manifest,
					$pub,
					$status->elements->$elementid,
					$activeElement,
					$collapse,
					$total,
					$master,
					$order
				);
			break;

			case 'freeze':
			case 'curator':
				$html = $this->drawItem(
					$elementid,
					$manifest,
					$pub,
					$status->elements->$elementid,
					$master,
					$viewname
				);
			break;
		}

		return $html;
	}

	/**
	 * Draw element without editing capabilities
	 *
	 * @param   integer  $elementid
	 * @param   object   $manifest
	 * @param   object   $pub
	 * @param   string   $status
	 * @param   object   $master
	 * @param   string   $viewname
	 * @return  object
	 */
	public function drawItem($elementId, $manifest, $pub = null, $status = null, $master = null, $viewname = 'freeze')
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'publications',
				'name'    =>'freeze',
				'layout'  =>'dataselector'
			)
		);

		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new \Components\Publications\Tables\Attachment($this->_parent->_db);
			$pub->_attachments = $pContent->sortAttachments($pub->version_id);
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view->type        = $manifest->params->type;
		$view->path        = $this->path;
		$view->pub         = $pub;
		$view->manifest    = $manifest;
		$view->status      = $status;
		$view->elementId   = $elementId;
		$view->attachments = $attachments;
		$view->database    = $this->_parent->_db;
		$view->master      = $master;
		$view->name        = $viewname;
		$view->viewer      = 'freeze';
		$view->git         = $this->_git;

		return $view->loadTemplate();
	}

	/**
	 * Draw file selector
	 *
	 * @param   integer  $elementid
	 * @param   object   $manifest
	 * @param   object   $pub
	 * @param   string   $status
	 * @param   integer  $active
	 * @param   integer  $collapse
	 * @param   integer  $total
	 * @param   object   $master
	 * @param   integer  $order
	 * @return  object
	 */
	public function drawSelector($elementId, $manifest, $pub = null, $status = null, $active = 0, $collapse = 0, $total = 0, $master = null, $order = 0)
	{
		// Get attachment type model
		$attModel = new \Components\Publications\Models\Attachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new \Components\Publications\Tables\Attachment($this->_parent->_db);
			$pub->_attachments = $pContent->sortAttachments ($pub->version_id);
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => 'publications',
				'name'    => 'blockelement',
				'layout'  => 'dataselector'
			)
		);

		$view->type        = $manifest->params->type;
		$view->path        = $this->path;
		$view->pub         = $pub;
		$view->manifest    = $manifest;
		$view->status      = $status;
		$view->elementId   = $elementId;
		$view->attachments = $attachments;
		$view->active      = $active;
		$view->collapse    = $collapse;
		$view->total       = $total;
		$view->master      = $master;
		$view->database    = $this->_parent->_db;
		$view->order       = $order;
		$view->viewer      = 'edit';
		$view->git         = $this->_git;

		return $view->loadTemplate();
	}
}
