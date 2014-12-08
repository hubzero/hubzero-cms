<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'helper.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'html.php');

/**
 * Handles a Datastore Lite attachment
 */
class PublicationsModelAttachmentData extends PublicationsModelAttachment
{
	/**
	* Attachment type name
	*
	* @var		string
	*/
	protected	$_name = 'data';

	/**
	* Image Helper
	*
	* @var
	*/
	protected	$_imgHelper = NULL;

	/**
	 * Get configs
	 *
	 * @return  boolean
	 */
	public function getConfigs( $element, $elementId, $pub, $blockParams )
	{
		$configs	= new stdClass;
		$typeParams = $element->typeParams;

		// Allow changes in non-draft version?
		$configs->freeze 	= isset($blockParams->published_editing)
							&& $blockParams->published_editing == 0
							&& ($pub->state == 1 || $pub->state == 5)
							? 1 : 0;
		// Get project path
		$config 		= JComponentHelper::getParams( 'com_projects' );
		$configs->path 	= ProjectsHelper::getProjectPath($pub->_project->alias,
						$config->get('webpath'),
						$config->get('offroot', 0));

		// Get publications helper
		$helper = new PublicationHelper($this->_parent->_db, $pub->version_id, $pub->id);

		$pubconfig = JComponentHelper::getParams( 'com_publications' );
		$base = $pubconfig->get('webpath');

		// Log path
		$configs->logPath = $helper->buildPath($pub->id, $pub->version_id, $base, 'logs', 0);

		// Get publication path
		$configs->pubBase = $helper->buildPath($pub->id, $pub->version_id, $base, '', 1);

		// Get publication path
		$configs->dataPath = $helper->buildPath($pub->id, $pub->version_id, $base, 'data', 1);

		// Serve path for data files
		/*$configs->servePath = JRoute::_('index.php?option=com_publications&id=' . $pub->id . '&task=serve&v=' . $pub->version_number);*/
		$configs->servePath = JRoute::_('index.php?option=com_publications' . a . 'id=' . $pub->id) . '/?vid=' . $pub->version_id . a . 'task=serve';

		// Get default title
		$title = isset($element->title) ? str_replace('{pubtitle}', $pub->title, $element->title) : NULL;
		$configs->title = str_replace('{pubversion}', $pub->version_label, $title);

		// Fancy launcher?
		$configs->fancyLauncher = isset($typeParams->fancyLauncher)
			? $typeParams->fancyLauncher : 0;

		return $configs;
	}

	/**
	 * Draw list
	 *
	 * @return  string HTML
	 */
	public function drawList( $attachments, $element, $elementId,
		$pub, $blockParams, $authorized)
	{
		// Get configs
		$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$html = '';

		$url =  JRoute::_('index.php?option=com_publications&task=serve&id='
				. $pub->id . '&v=' . $pub->version_number . '&el=' . $elementId );
		$url = preg_replace('/\/administrator/', '', $url);

		if ($attachments)
		{
			// Serve individually
			foreach ($attachments as $attach)
			{
				if ($attach->type == 'data')
				{
					continue;
				}
				$itemUrl 	= $url . '&a=' . $attach->id;
				$title 		= $attach->title ? $attach->title : $configs->title;
				$title 		= $title ? $title : $attach->path;
				$pop		= JText::_('Browse database') . ' ' . $title;

				$html .= '<li>';
				$html .= $authorized === 'administrator' ? '[' . $this->_name . '] ' : '';
				$html .= '<a href="' . $itemUrl . '" title="' . $pop . '" target="_blank">' . $title . '</a>';
				$html .='</li>';
			}
		}

		return $html;
	}

	/**
	 * Draw launcher
	 *
	 * @return  string HTML
	 */
	public function drawLauncher( $element, $elementId, $pub, $blockParams, $elements, $authorized )
	{
		// Get configs
		$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId])
					 ? $attachments['elements'][$elementId] : NULL;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments(
			$elementId,
			$attachments,
			$this->_name
		);

		$disabled = 0;
		$pop 	  = NULL;

		if ($pub->state == 0)
		{
			$pop 		= JText::_('COM_PUBLICATIONS_STATE_UNPUBLISHED_POP');
			$disabled 	= 1;
		}
		elseif (!$authorized)
		{
			$pop = $pub->access == 1
			     ? JText::_('COM_PUBLICATIONS_STATE_REGISTERED_POP')
			     : JText::_('COM_PUBLICATIONS_STATE_RESTRICTED_POP');
			$disabled = 1;
		}
		elseif (!$attachments)
		{
			$disabled = 1;
			$pop = JText::_('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE');
		}

		$pop   = $pop ? '<p class="warning">' . $pop . '</p>' : '';

		$html = '';

		// Which role?
		$role = $element->params->role;

		$url = JRoute::_('index.php?option=com_publications&task=serve&id='
				. $pub->id . '&v=' . $pub->version_number )
				. '?el=' . $elementId;

		// Primary button
		if ($role == 1)
		{
			$attach = $attachments[0];
			if (count($attachments) > 1)
			{
				// TBD
			}

			// One launcher for all items
			$label = JText::_('View publication');
			$class = 'btn btn-primary active icon-next';
			$class .= $disabled ? ' link_disabled' : '';
			$title = $configs->title ? $configs->title : JText::_('View publication');
			$html  = PublicationsHtml::primaryButton($class, $url, $label, NULL,
					$title, 'rel="external"', $disabled, $pop);
		}
		elseif ($role == 2 && $attachments)
		{
			$html .= '<ul>';
			$html .= self::drawList( $attachments, $element, $elementId,
					$pub, $blockParams, $authorized);
			$html .= '</ul>';
		}

		return $html;
	}

	/**
	 * Transfer files from one version to another
	 *
	 * @return  boolean
	 */
	public function transferData( $elementparams, $elementId, $pub, $blockParams,
			$attachments, $oldVersion, $newVersion)
	{
		// Get databases plugin
		JPluginHelper::importPlugin( 'projects', 'databases');
		$dispatcher = JDispatcher::getInstance();

		// Get configs
		$configs = $this->getConfigs($elementparams, $elementId, $pub, $blockParams);

		$juser = JFactory::getUser();

		$newConfigs = new stdClass;
		$newConfigs->path = $configs->path;
		$newConfigs->dataPath = $pub->_helpers->pubHelper->buildPath(
			$pub->id,
			$newVersion->id,
			'',
			'data',
			1
		);
		$newConfigs->servePath = JRoute::_('index.php?option=com_publications' . a . 'id=' . $pub->id) . '/?vid=' . $newVersion->id . a . 'task=serve';

		// Loop through attachments
		foreach ($attachments as $att)
		{
			if ($att->type != $this->_name)
			{
				continue;
			}
			// Get database object and load record
			$objData = new ProjectDatabase($this->_parent->_db);
			$objData->loadRecord($att->object_name);
			$dbVersion = NULL;

			if (!$objData->id)
			{
				// Original database not found
				$this->_parent->setError( JText::_('Oups! Cannot attach selected database: database not found') );
				return false;
			}

			// Make new attachment record
			$pAttach = new PublicationAttachment( $this->_parent->_db );
			if (!$pAttach->copyAttachment($att, $newVersion->id, $elementId, $juser->get('id') ))
			{
				continue;
			}

			// New database instance - need to clone again and get a new version number
			$result 			= $dispatcher->trigger( 'clone_database', array( $pAttach->object_name, $pub->_project, $newConfigs->servePath) );
			$dbVersion 			= $result && isset($result[0]) ? $result[0] : NULL;

			// Failed to clone
			if (!$dbVersion)
			{
				$this->_parent->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_FAILED_DB_CLONE') );
				$pAttach->delete();
				return false;
			}

			$pAttach->modified_by = NULL;
			$pAttach->modified 	= NULL;
			$pAttach->object_revision = $dbVersion;
			$pAttach->path = 'dataviewer' . DS . 'view' . DS . 'publication:dsl'
										. DS . $pAttach->object_name . DS . '?v=' . $dbVersion;
			$pAttach->store();
		}

		// Determine accompanying files and copy them in the right location
		$this->publishDataFiles($objData, $newConfigs);

		return true;
	}

	/**
	 * Serve
	 *
	 * @return  boolean
	 */
	public function serve( $element, $elementId, $pub, $blockParams, $itemId = 0)
	{
		// Get configs
		$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments($elementId, $attachments, $this->_name);

		$path = NULL;
		if ($itemId)
		{
			foreach ($attachments as $attach)
			{
				if ($attach->id == $itemId)
				{
					$path = $attach->path;
				}
			}
		}
		else
		{
			$attach = $attachments[0];
			$path   = $attach->path;
		}

		if (!$path)
		{
			$this->setError( JText::_('Oups! Something went wrong. Cannot redirect to content.') );
			return false;
		}

		$v = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";

		// Absolute or relative link?
		$where = preg_match($v, $path) ? $path : DS . trim($path, DS);
		$this->_parent->set('redirect', $where);
		return true;
	}

	/**
	 * Save incoming
	 *
	 * @return  boolean
	 */
	public function save( $element, $elementId, $pub, $blockParams, $toAttach = array() )
	{
		// Incoming selections
		if (empty($toAttach))
		{
			$selections = JRequest::getVar( 'selecteditems', '');
			$toAttach = explode(',', $selections);
		}

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Nothing to change
		if (empty($toAttach))
		{
			return false;
		}

		// Create new version path
		if (!is_dir( $configs->dataPath ))
		{
			if (!JFolder::create( $configs->dataPath ))
			{
				$this->_parent->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return false;
			}
		}

		// Get actor
		$juser = JFactory::getUser();
		$uid   = $juser->get('id');
		if (!$uid)
		{
			return false;
		}

		// Counters
		$i = 0;
		$a = 0;

		// Attach/refresh each selected item
		foreach ($toAttach as $identifier)
		{
			if (!trim($identifier))
			{
				continue;
			}

			$a++;
			$ordering = $i + 1;

			if ($this->addAttachment($identifier, $pub, $configs, $uid, $elementId, $element, $ordering))
			{
				$i++;
			}
		}

		// Success
		if ($i > 0 && $i == $a)
		{
			$message = $this->get('_message') ? $this->get('_message') : JText::_('Selection successfully saved');
			$this->set('_message', $message);
		}

		return true;
	}

	/**
	 * Add/edit attachment
	 *
	 *
	 * @return     boolean or error
	 */
	public function addAttachment($database_name, $pub, $configs, $uid, $elementId, $element, $ordering = 1)
	{
		// Get databases plugin
		JPluginHelper::importPlugin( 'projects', 'databases');
		$dispatcher = JDispatcher::getInstance();

		// Get database object and load record
		$objData = new ProjectDatabase($this->_parent->_db);
		$objData->loadRecord($database_name);
		$dbVersion = NULL;

		$objPA = new PublicationAttachment( $this->_parent->_db );
		if ($objPA->loadElementAttachment($pub->version_id, array( 'object_name' => $database_name),
			$elementId, $this->_name, $element->role))
		{
			// Already attached
			$new = 0;

			if (!$objData->id)
			{
				// Original got deleted, can't do much
				return true;
			}
		}
		else
		{
			if (!$objData->id)
			{
				// Original database not found
				$this->setError( JText::_('Oups! Cannot attach selected database: database not found') );
				return false;
			}
			$objPA->publication_id 			= $pub->id;
			$objPA->publication_version_id 	= $pub->version_id;
			$objPA->type 					= $this->_name;
			$objPA->created_by 				= $uid;
			$objPA->created 				= JFactory::getDate()->toSql();
			$objPA->role 					= $element->role;
			$new = 1;

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}

		if ($new)
		{
			$result = $dispatcher->trigger( 'clone_database', array( $database_name, $pub->_project, $configs->servePath) );
			$dbVersion = $result && isset($result[0]) ? $result[0] : NULL;
		}
		else
		{
			$rtime = $objPA->modified ? strtotime($objPA->modified) : NULL;
			if ($objPA->object_id != $objData->id || strtotime($objData->updated) > $rtime )
			{
				// New database instance - need to clone again and get a new version number
				$result 			= $dispatcher->trigger( 'clone_database', array( $database_name, $pub->_project, $configs->servePath) );
				$dbVersion 			= $result && isset($result[0]) ? $result[0] : NULL;
				$objPA->modified_by = $uid;
				$objPA->modified 	= JFactory::getDate()->toSql();
			}
			else
			{
				// No changes
				$dbVersion = $objPA->object_revision;
			}
		}
		// Failed to clone
		if (!$dbVersion)
		{
			$this->_parent->setError( JText::_('PLG_PROJECTS_PUBLICATIONS_ERROR_FAILED_DB_CLONE') );
			return false;
		}

		$objPA->object_id   	= $objData->id;
		$objPA->object_name 	= $database_name;
		$objPA->object_revision = $dbVersion;
		$objPA->element_id 		= $elementId;
		$objPA->ordering 		= $ordering;
		$objPA->title 			= $objPA->title ? $objPA->title : $objData->title;

		// Build link path
		$objPA->path 			= 'dataviewer' . DS . 'view' . DS . 'publication:dsl'
									. DS . $database_name . DS . '?v=' . $dbVersion;

		if (!$objPA->store())
		{
			$this->_parent->setError(JText::_('There was a problem attaching the database'));
			return false;
		}
		// Determine accompanying files and copy them in the right location
		$this->publishDataFiles($objData, $configs);

		return true;
	}

	/**
	 * Remove attachment
	 *
	 *
	 * @return     boolean or error
	 */
	public function removeAttachment($row, $element, $elementId, $pub, $blockParams)
	{
		$juser = JFactory::getUser();
		$uid   = $juser->get('id');

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Remove link
		if (!$this->getError())
		{
			// Get databases plugin
			JPluginHelper::importPlugin( 'projects', 'databases');
			$dispatcher = JDispatcher::getInstance();

			// Get database object and load record
			$objData = new ProjectDatabase($this->_parent->_db);
			$objData->loadRecord($row->object_name);
			if (!$objData->id)
			{
				$this->_parent->setError(JText::_('There was a problem deleting the database'));
				return false;
			}

			$result = $dispatcher->trigger( 'remove_database', array( $row->object_name, $pub->_project, $row->object_revision) );

			$row->delete();

			$this->set('_message', JText::_('Item removed'));

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);

			return true;
		}

		return false;
	}

	/**
	 * Update attachment properties
	 *
	 * @return     boolean or error
	 */
	public function updateAttachment($row, $element, $elementId, $pub, $blockParams)
	{
		// Incoming
		$title 	= JRequest::getVar( 'title', '' );
		$thumb 	= JRequest::getInt( 'makedefault', 0 );

		$juser = JFactory::getUser();
		$uid   = $juser->get('id');

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Update label
		$row->title 		= $title;
		$row->modified_by 	= $uid;
		$row->modified 		= JFactory::getDate()->toSql();

		// Update record
		if (!$row->store())
		{
			$this->setError(JText::_('Error updating item record'));
		}

		$this->set('_message', JText::_('Update successful'));

		return true;
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $element, $attachments )
	{
		$status = new PublicationsModelStatus();

		// Get requirements to check against
		$max 		= $element->max;
		$min 		= $element->min;
		$role 		= $element->role;
		$params		= $element->typeParams;
		$required	= $element->required;
		$counter 	= count($attachments);

		if (!$required)
		{
			$status->status = $counter ? 1 : 2;
			return $status;
		}

		// Check for correct number of attachments
		if ($min > 0 && $counter < $min)
		{
			if ($counter)
			{
				$status->setError( JText::_('Need at least ' . $min . ' attachment') );
			}
			else
			{
				// No Attachments
				$status->status = 0;
				return $status;
			}
		}
		elseif ($max > 0 && $counter > $max)
		{
			$status->setError( JText::_('Maximum ' . $max . ' attachment(s) allowed') );
		}

		$status->status = $status->getError() ? 0 : 1;

		return $status;
	}

	/**
	 * Build Data object
	 *
	 * @return  HTML string
	 */
	public function buildDataObject($att, $view, $i = 1)
	{
		$data 			= new stdClass;
		$data->row 		= $att;
		$data->ordering = $i;
		$data->editUrl  = $view->editUrl;
		$data->id		= $att->id;
		$data->props	= $view->master->block . '-' . $view->master->sequence . '-' . $view->elementId;
		$data->viewer	= $view->viewer;
		$data->version	= $view->pub->version_number;
		return $data;
	}

	/**
	 * Draw attachment
	 *
	 * @return  HTML string
	 */
	public function drawAttachment($data, $params)
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'attachments',
				'layout'	=> $this->_name
			)
		);
		$view->data 	= $data;
		$view->params   = $params;

		if ($this->getError())
		{
			$view->setError( $this->getError() );
		}
		return $view->loadTemplate();
	}

	/**
	 * Add to zip bundle
	 *
	 * @return  boolean
	 */
	public function addToBundle( $zip, $attachments, $element, $elementId,
		$pub, $blockParams, &$readme, $bundleDir)
	{
		if (!$attachments)
		{
			return false;
		}

		// Get configs
		$configs  = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$firstChild = $attachments[0];
		$db_name 	= $firstChild->object_name;
		$db_version = $firstChild->object_revision;

		// Add CSV file
		if ($db_name && $db_version)
		{
			$tmpFile 	= $configs->dataPath . DS . 'data.csv';
			$csv 		= $this->getCsvData($db_name, $db_version, $tmpFile);

			if ($csv && file_exists($tmpFile))
			{
				$where = $bundleDir . DS . 'data.csv';
				if ($zip->addFile($tmpFile, $where))
				{
					$readme   .= '>>> ' . str_replace($bundleDir . DS, '', $where) . "\n";
				}
			}
		}

		// Add data files
		$dataFiles = array();
		if (is_dir($configs->dataPath))
		{
			$dataFiles = JFolder::files($configs->dataPath, '.', true, true);
		}

		if (!empty($dataFiles))
		{
			foreach ($dataFiles as $e)
			{
				// Skip thumbnails and CSV
				if (preg_match("/_tn.gif/", $e) || preg_match("/_medium.gif/", $e) || preg_match("/data.csv/", $e))
				{
					continue;
				}

				$fileinfo = pathinfo($e);
				$a_dir  = $fileinfo['dirname'];
				$a_dir	= trim(str_replace($configs->dataPath, '', $a_dir), DS);

				$fPath = $a_dir && $a_dir != '.' ? $a_dir . DS : '';
				$where = $bundleDir . DS . 'data' . DS . $fPath . basename($e);

				if ($zip->addFile($e, $where))
				{
					$readme   .= '>>> ' . str_replace($bundleDir . DS, '', $where) . "\n";
				}
			}
		}

		return false;
	}

	/**
	 * Get data as CSV file
	 *
	 * @param      string  	$db_name
	 * @param      integer  	$version
	 *
	 * @return     string data
	 */
	public function getCsvData($db_name = '', $version = '', $tmpFile = '')
	{
		if (!$db_name || !$version)
		{
			return false;
		}

		mb_internal_encoding('UTF-8');

		// component path for "com_dataviewer"
		$dv_com_path = JPATH_ROOT . DS . 'components' . DS . 'com_dataviewer';

		require_once($dv_com_path . DS . 'dv_config.php');
		require_once($dv_com_path . DS . 'lib' . DS . 'db.php');
		require_once($dv_com_path . DS . 'modes' . DS . 'mode_dsl.php');
		require_once($dv_com_path . DS . 'filter' . DS . 'csv.php');

		$dv_conf = get_conf(NULL);
		$dd = get_dd(NULL, $db_name, $version);
		$dd['serverside'] = false;

		$sql = query_gen($dd);
		$result = get_results($sql, $dd);

		ob_start();
		filter($result, $dd, true);
		$csv = ob_get_contents();
		ob_end_clean();

		if ($csv && $tmpFile)
		{
			$handle = fopen($tmpFile, 'w');
			fwrite($handle, $csv);
			fclose($handle);

			return true;
		}

		return $csv;
	}

	/**
	 * Draw list
	 *
	 * @return  boolean
	 */
	public function drawPackageList( $attachments, $element, $elementId,
		$pub, $blockParams, $authorized)
	{
		// Get configs
		$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$list = NULL;

		if (!$attachments)
		{
			return false;
		}

		$list .= '<li><img src="' . ProjectsHtml::getFileIcon('csv') . '" alt="" /> data.csv</li>';

		// Add data files
		$dataFiles = array();
		if (is_dir($configs->dataPath))
		{
			$dataFiles = JFolder::files($configs->dataPath, '.', true, true);
		}
		if (!empty($dataFiles))
		{
			$list .= '<li><img src="/plugins/projects/files/images/folder.gif" alt="" /> data</li>';
			foreach ($dataFiles as $e)
			{
				// Skip thumbnails and CSV
				if (preg_match("/_tn.gif/", $e) || preg_match("/_medium.gif/", $e) || preg_match("/data.csv/", $e))
				{
					continue;
				}
				// Get ext
				$parts  = explode('.', $e);
				$ext 	= count($parts) > 1 ? array_pop($parts) : NULL;
				$ext	= strtolower($ext);
				$icon   = '<img src="' . ProjectsHtml::getFileIcon($ext) . '" alt="'.$ext.'" />';
				$fileinfo = pathinfo($e);
				$a_dir  = $fileinfo['dirname'];
				$a_dir	= trim(str_replace($configs->dataPath, '', $a_dir), DS);

				$fPath = $a_dir && $a_dir != '.' ? $a_dir . DS : '';
				$where = 'data' . DS . $fPath . basename($e);

				$list .= '<li class="level2"><span class="item-title">' . $icon . ' ' . trim($where, DS) . '</span></li>';
			}
		}

		return $list;
	}

	/**
	 * Publish supporting database files
	 *
	 * @param      object  	$objPD
	 *
	 * @return     boolean or error
	 */
	public function publishDataFiles($objPD, $configs)
	{
		if (!$objPD->id)
		{
			return false;
		}

		// Get files plugin
		JPluginHelper::importPlugin( 'projects', 'files');
		$dispatcher = JDispatcher::getInstance();

		// Get data definition
		$dd = json_decode($objPD->data_definition, true);

		$files 	 = array();
		$columns = array();

		foreach ($dd['cols'] as $colname => $col)
		{
			if (isset($col['linktype']) && $col['linktype'] == "repofiles")
			{
				$dir = '';
				if (isset($col['linkpath']) && $col['linkpath'] != '')
				{
					$dir = $col['linkpath'];
				}
				$columns[$col['idx']] = $dir;
			}
		}

		// No files to publish
		if (empty($columns))
		{
			return false;
		}

		$repoPath = $objPD->source_dir ? $configs->path . DS . $objPD->source_dir : $configs->path;
		$csv = $repoPath . DS . $objPD->source_file;

		if (file_exists($csv) && ($handle = fopen($csv, "r")) !== FALSE)
		{
			// Check if expert mode CSV
			$expert_mode = false;
			$col_labels = fgetcsv($handle);
			$col_prop = fgetcsv($handle);
			$data_start = fgetcsv($handle);

			if (isset($data_start[0]) && $data_start[0] == 'DATASTART')
			{
				$expert_mode = true;
			}

			while ($r = fgetcsv($handle))
			{
				for ($i = 0; $i < count($col_labels); $i++)
				{
					if (isset($columns[$i]))
					{
						if ((isset($r[$i]) && $r[$i] != ''))
						{
							$file = $columns[$i] ? $columns[$i] . DS . trim($r[$i]) : trim($r[$i]);
							if (file_exists( $repoPath . DS . $file))
							{
								$files[] = $file;
							}
						}
					}
				}
			}
		}

		// Copy files from repo to published location
		if (!empty($files))
		{
			foreach ($files as $file)
			{
				if (!file_exists( $repoPath . DS . $file))
				{
					continue;
				}

				// If parent dir does not exist, we must create it
				if (!file_exists(dirname($configs->dataPath . DS . $file)))
				{
					JFolder::create(dirname($configs->dataPath . DS . $file));
				}

				JFile::copy($repoPath . DS . $file, $configs->dataPath . DS . $file);

				// Generate thumbnails for images
				$thumb 	= PublicationsHtml::createThumbName($file, '_tn', $extension = 'gif');
				$dispatcher->trigger( 'getFilePreview', array(
					$file, '', $repoPath, '', NULL, false, $configs->dataPath, $thumb, 180, 180)
				);
				// Medium size thumb
				$medium 	= PublicationsHtml::createThumbName($file, '_medium', $extension = 'gif');
				$dispatcher->trigger( 'getFilePreview', array(
					$file, '', $repoPath, '', NULL, true, $configs->dataPath, $medium)
				);
			}
		}
	}
}