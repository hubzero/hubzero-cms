<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
 * Renders URL selector element
 */
class PublicationsModelBlockElementDataselector extends PublicationsModelBlockElement
{
	/**
	* Element name
	*
	* @var		string
	*/
	protected	$_name = 'dataselector';

	/**
	* Git helper
	*
	* @var		string
	*/
	protected	$_git = NULL;

	/**
	* Project repo path
	*
	* @var		string
	*/
	protected	$path = NULL;

	/**
	 * Render
	 *
	 * @return  object
	 */
	public function render( $elementid, $manifest, $pub = NULL, $viewname = 'edit',
		$status = NULL, $master = NULL, $order = 0 )
	{
		$html = '';

		// Get project path
		$config 	= JComponentHelper::getParams( 'com_projects' );
		$this->path = ProjectsHelper::getProjectPath($pub->_project->alias,
					  $config->get('webpath'), $config->get('offroot'));

		$showElement 	= $master->props['showElement'];
		$total 			= $master->props['total'];

		// Incoming
		$activeElement  = JRequest::getInt( 'el', $showElement );

		// Git helper
		if (!$this->_git)
		{
			include_once( JPATH_ROOT . DS . 'components' . DS
				. 'com_projects' . DS . 'helpers' . DS . 'githelper.php' );
			$this->_git = new ProjectsGitHelper(
				$config->get('gitpath', '/opt/local/bin/git'),
				0,
				$config->get('offroot', 0) ? '' : JPATH_ROOT);
		}

		// Do we need to collapse inactive elements?
		$collapse = isset($master->params->collapse_elements) && $master->params->collapse_elements ? 1 : 0;

		switch ($viewname)
		{
			case 'edit':
			default:
				$html = $this->drawSelector( $elementid, $manifest, $pub,
						$status->elements->$elementid, $activeElement,
						$collapse, $total, $master, $order
				);

			break;

			case 'freeze':
			case 'curator':
				$html = $this->drawItem( $elementid, $manifest, $pub,
						$status->elements->$elementid, $master, $viewname
				);
			break;
		}

		return $html;
	}

	/**
	 * Draw element without editing capabilities
	 *
	 * @return  object
	 */
	public function drawItem( $elementId, $manifest, $pub = NULL,
		$status = NULL, $master = NULL, $viewname = 'freeze')
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'freeze',
				'layout'	=>'dataselector'
			)
		);

		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view->type 		 = $manifest->params->type;
		$view->path			 = $this->path;
		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->attachments	 = $attachments;
		$view->database		 = $this->_parent->_db;
		$view->master		 = $master;
		$view->name			 = $viewname;
		$view->viewer		 = 'freeze';
		$view->git			 = $this->_git;

		return $view->loadTemplate();
	}

	/**
	 * Draw file selector
	 *
	 * @return  object
	 */
	public function drawSelector( $elementId, $manifest, $pub = NULL, $status = NULL,
		$active = 0, $collapse = 0, $total = 0,
		$master = NULL, $order = 0)
	{
		// Get attachment type model
		$attModel = new PublicationsModelAttachments($this->_parent->_db);

		// Make sure we have attachments
		if (!isset($pub->_attachments))
		{
			// Get attachments
			$pContent = new PublicationAttachment( $this->_parent->_db );
			$pub->_attachments = $pContent->sortAttachments ( $pub->version_id );
		}

		// Get attached items
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;
		$attachments = $attModel->getElementAttachments($elementId, $attachments,
					   $manifest->params->type, $manifest->params->role);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=> 'projects',
				'element'	=> 'publications',
				'name'		=> 'blockelement',
				'layout'	=> 'dataselector'
			)
		);

		$view->type 		 = $manifest->params->type;
		$view->path			 = $this->path;
		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->attachments	 = $attachments;
		$view->active		 = $active;
		$view->collapse		 = $collapse;
		$view->total		 = $total;
		$view->master 		 = $master;
		$view->database		 = $this->_parent->_db;
		$view->order		 = $order;
		$view->viewer		 = 'edit';
		$view->git			 = $this->_git;

		return $view->loadTemplate();
	}
}