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
class PublicationsModelAttachmentTool extends PublicationsModelAttachment
{
	/**
	* Attachment type name
	*
	* @var		string
	*/
	protected	$_name = 'tool';

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
				$itemUrl 	= $url . '&a=' . $attach->id;
				$title 		= $attach->title ? $attach->title : $configs->title;
				$title 		= $title ? $title : $attach->path;
				$pop		= JText::_('Launch tool') . ' ' . $title;

				$html .= '<li>';
				$html .= $authorized === 'administrator' ? '[' . $this->_name . '] ' : '';
				$html .= '<a href="' . $itemUrl . '" title="' . $pop . '" target="_blank" class="data-type">' . $title . '</a>';
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

		$mconfig = JComponentHelper::getParams('com_tools');

		// Ensure we have a connection to the middleware
		if (!$mconfig->get('mw_on'))
		{
			$pop 		= JText::_('COM_PUBLICATIONS_STATE_SESSION_INVOKE_DISABLED_POP');
			$disabled 	= 1;
		}
		elseif ($pub->state == 0)
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
			$label = JText::_('COM_PUBLICATIONS_LAUNCH_TOOL');
			$class = 'btn btn-primary active icon-next';
			$class .= $disabled ? ' link_disabled' : '';
			$title = $configs->title ? $configs->title : JText::_('COM_PUBLICATIONS_LAUNCH_TOOL');
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
		$juser = JFactory::getUser();

		// Loop through attachments
		foreach ($attachments as $att)
		{
			// Make new attachment record
			$pAttach = new PublicationAttachment( $this->_parent->_db );
			if (!$pAttach->copyAttachment($att, $newVersion->id, $elementId, $juser->get('id') ))
			{
				continue;
			}
		}

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

		// Default to first attachment
		$record = count($attachments) > 0 ? $attachments[0] : NULL;

		// Specific attachment requested?
		if ($itemId)
		{
			foreach ($attachments as $attach)
			{
				if ($attach->id == $itemId)
				{
					$record = $attach;
				}
			}
		}
		// Attachment missing
		if (!$record)
		{
			$this->setError( JText::_('Oups! Something went wrong. Cannot redirect to content.') );
			return false;
		}

		//are we on the iPad
		$isiPad = (bool) strpos($_SERVER['HTTP_USER_AGENT'], 'iPad');

		//get tool params
		$params = JComponentHelper::getParams('com_tools');
		$launchOnIpad = $params->get('launch_ipad', 0);

		// Generate the URL that launches a tool session
		$v = $record->object_revision ? $record->object_revision : 'dev';
		if ($isiPad && $launchOnIpad)
		{
			$path = 'nanohub://tools/invoke/' . $record->object_name . '/' . $v;
		}
		else
		{
			$path = JRoute::_('index.php?option=com_tools&app=' . $record->object_name . '&task=invoke&version=' . $v);
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
	public function addAttachment($toolObject, $pub, $configs, $uid, $elementId, $element, $ordering = 1)
	{
		// TBD
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
		// TBD
		return true;
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
		return false;
	}

	/**
	 * Draw list
	 *
	 * @return  boolean
	 */
	public function drawPackageList( $attachments, $element, $elementId,
		$pub, $blockParams, $authorized)
	{
		return false;
	}
}