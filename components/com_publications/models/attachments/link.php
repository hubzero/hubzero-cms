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

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'helper.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_projects'
	. DS . 'helpers' . DS . 'html.php');

/**
 * Handles a link attachment
 */
class PublicationsModelAttachmentLink extends PublicationsModelAttachment
{
	/**
	* Attachment type name
	*
	* @var		string
	*/
	protected	$_name = 'link';

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

		// Get publications helper
		$helper = new PublicationHelper($this->_parent->_db, $pub->version_id, $pub->id);

		// Log path
		$configs->logPath = $helper->buildPath($pub->id, $pub->version_id, '', 'logs', 0);

		// replace current attachments?
		$configs->replace  	= JRequest::getInt( 'replace_current', 0, 'post');

		// Verify file type against allowed before attaching?
		$configs->check = isset($blockParams->verify_types) ? $blockParams->verify_types : 0;

		// Get default title
		$configs->title = isset($element->title) ? str_replace('{pubtitle}', $pub->title, $element->title) : NULL;

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
				$pop		= JText::_('View link') . ' ' . $title;

				$html .= '<li>';
				$html .= $authorized == 'administrator' ? '[' . $this->_name . '] ' : '';
				$html .= '<a href="' . $itemUrl . '" title="' . $pop . '" target="_blanl">' . $title . '</a>';
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
	public function drawLauncher( $element, $elementId, $pub, $blockParams, $authorized)
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

			// One launcher for all files
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
		$juser = JFactory::getUser();

		// Loop through attachments
		foreach ($attachments as $att)
		{
			// Make new attachment record
			$pAttach = new PublicationAttachment( $this->_parent->_db );
			$pAttach->publication_id 		= $att->publication_id;
			$pAttach->title 				= $att->title;
			$pAttach->role 					= $att->role;
			$pAttach->element_id 			= $elementId;
			$pAttach->path 					= $att->path;
			$pAttach->vcs_hash 				= $att->vcs_hash;
			$pAttach->vcs_revision 			= $att->vcs_revision;
			$pAttach->object_id 			= $att->object_id;
			$pAttach->object_name 			= $att->object_name;
			$pAttach->object_instance 		= $att->object_instance;
			$pAttach->object_revision 		= $att->object_revision;
			$pAttach->type 					= $att->type;
			$pAttach->params 				= $att->params;
			$pAttach->attribs 				= $att->attribs;
			$pAttach->ordering 				= $att->ordering;
			$pAttach->publication_version_id= $newVersion->id;
			$pAttach->created_by 			= $juser->get('id');
			$pAttach->created 				= JFactory::getDate()->toSql();
			if (!$pAttach->store())
			{
				continue;
			}
		}
	}

	/**
	 * Serve
	 *
	 * @return  boolean
	 */
	public function serve( $element, $elementId, $pub, $blockParams, $itemId = 0)
	{
		// Incoming
		$forceDownload = JRequest::getInt( 'download', 0 );		// Force downlaod action?

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
		$toAttach   = $toAttach ? $toAttach : JRequest::getVar( 'url', '', 'post', 'array');
		$titles 	= JRequest::getVar( 'title', '', 'post', 'array');
		$desc 		= JRequest::getVar( 'desc', '', 'post', 'array');

		// Incoming selections
		if (empty($toAttach))
		{
			$toAttach = array($url);
		}

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Nothing to change
		if (empty($toAttach) && !$configs->replace)
		{
			return false;
		}

		// Get existing attachments for the elemnt
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments($elementId, $attachments, $this->_name);

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

		// Default title for publication
		$defaultTitle = $pub->_curationModel->_manifest->params->default_title;

		// Attach/refresh each selected item
		foreach ($toAttach as $identifier)
		{
			if (!trim($identifier))
			{
				continue;
			}

			$a++;
			$ordering = $i + 1;

			$title = isset($titles[$i]) ? $titles[$i] : NULL;
			$desc  = isset($desc[$i]) ? $desc[$i] : NULL;

			if ($this->addAttachment($identifier, $title, $pub, $configs, $uid, $elementId, $element, $ordering))
			{
				// Do we also set draft title and metadata from the link?
				if ($i == 0 && $title && $element->role == 1
					&& stripos($pub->title, $defaultTitle) !== false )
				{
					// Load publication version
					$row = new PublicationVersion( $this->_parent->_db );
					if (!$row->load($pub->version_id))
					{
						$this->setError(JText::_('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_VERSION_NOT_FOUND'));
						return false;
					}

					$row->title    		= \Hubzero\Utility\Sanitize::clean(htmlspecialchars($title));
					$description	   	= \Hubzero\Utility\Sanitize::clean(htmlspecialchars($desc));
					$row->description 	= $description;
					$row->abstract		= \Hubzero\Utility\String::truncate($description, 255);
					$row->store();
				}

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
	 * Add/edit file attachment
	 *
	 *
	 * @return     boolean or error
	 */
	public function addAttachment($path, $title, $pub, $configs, $uid, $elementId, $element, $ordering = 1)
	{
		// Need to check against allowed types
		if ($configs->check)
		{
			if (!$this->checkAllowed(array($path), $element->typeParams->accept))
			{
				return false;
			}
		}

		$objPA = new PublicationAttachment( $this->_parent->_db );
		if ($objPA->loadElementAttachment($pub->version_id, array( 'path' => $path),
			$elementId, $this->_name, $element->role))
		{
			// Link already attached
			$this->setError(JText::_('The link is already attached'));
			return true;
		}
		else
		{
			$objPA->publication_id 			= $pub->id;
			$objPA->publication_version_id 	= $pub->version_id;
			$objPA->path 					= $path;
			$objPA->type 					= $this->_name;
			$objPA->created_by 				= $uid;
			$objPA->created 				= JFactory::getDate()->toSql();
			$objPA->role 					= $element->role;
			$objPA->title 					= $title;

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}

		$objPA->element_id 	= $elementId;
		$objPA->ordering 	= $ordering;

		if (!$objPA->store())
		{
			$this->setError(JText::_('There was a problem attaching the link'));
			return false;
		}

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
			$row->delete();
			$this->set('_message', JText::_('Item removed'));

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);

			return true;
		}

		return false;
	}

	/**
	 * Update file attachment properties
	 *
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
		$allowed 	= isset($params->accept) ? $params->accept :  NULL;

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
				// No files
				$status->status = 0;
				return $status;
			}
		}
		elseif ($max > 0 && $counter > $max)
		{
			$status->setError( JText::_('Maximum ' . $max . ' attachment(s) allowed') );
		}
		// Check allowed formats
		elseif (!self::checkAllowed($attachments, $allowed))
		{
			if ($counter && !empty($accept))
			{
				$error = JText::_('Error: unacceptable URL. URL should start with: ');
				foreach ($params->allowed_ext as $ext)
				{
					$error .= ' ' . $ext .',';
				}
				$error = substr($error, 0, strlen($error) - 1);
				$status->setError( $error );
			}
		}

		$status->status = $status->getError() ? 0 : 1;

		return $status;
	}

	/**
	 * Check for allowed formats
	 *
	 * @return  object
	 */
	public function checkAllowed( $attachments, $formats = array() )
	{
		if (empty($formats))
		{
			return true;
		}

		foreach ($attachments as $attach)
		{
			$path = isset($attach->path) ? $attach->path : $attach;
			foreach ($formats as $f)
			{
				if (stripos($path, $f) !== false)
				{
					return true;
				}
			}

		}

		return false;
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
}