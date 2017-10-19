<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

namespace Components\Publications\Models\Attachment;

use Components\Publications\Models\Attachment as Base;
use stdClass;

/**
 * Handles a tool attachment
 */
class Tool extends Base
{
	/**
	 * Attachment type name
	 *
	 * @var		string
	 */
	protected	$_name = 'tool';

	/**
	 * Unique attachment properties
	 *
	 * @var array
	 */
	protected $_connector  = array('object_name', 'object_instance');

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
		$configs->path 	= $pub->_project->repo()->get('path');

		$pubconfig = Component::params( 'com_publications' );
		$base = $pubconfig->get('webpath');

		// Log path
		$configs->logPath = \Components\Publications\Helpers\Html::buildPubPath($pub->id, $pub->version_id, $base, 'logs', 0);

		// Get publication path
		$configs->pubBase = \Components\Publications\Helpers\Html::buildPubPath($pub->id, $pub->version_id, $base, '', 1);

		// Get default title
		$title = isset($element->title) ? str_replace('{pubtitle}', $pub->title, $element->title) : null;
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

		$url =  Route::url('index.php?option=com_publications&task=serve&id='
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
				$pop		= Lang::txt('Launch tool') . ' ' . $title;

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
					 ? $attachments['elements'][$elementId] : null;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments(
			$elementId,
			$attachments,
			$this->_name
		);

		$disabled = 0;
		$pop 	  = null;

		$mconfig = Component::params('com_tools');

		// Ensure we have a connection to the middleware
		if (!$mconfig->get('mw_on'))
		{
			$pop 		= Lang::txt('COM_PUBLICATIONS_STATE_SESSION_INVOKE_DISABLED_POP');
			$disabled 	= 1;
		}
		elseif ($pub->isUnpublished() || $pub->isDown())
		{
			$pop 		= Lang::txt('COM_PUBLICATIONS_STATE_UNPUBLISHED_POP');
			$disabled 	= 1;
		}
		elseif (!$authorized)
		{
			$pop = $pub->access == 1
			     ? Lang::txt('COM_PUBLICATIONS_STATE_REGISTERED_POP')
			     : Lang::txt('COM_PUBLICATIONS_STATE_RESTRICTED_POP');
			$disabled = 1;
		}
		elseif (!$attachments)
		{
			$disabled = 1;
			$pop = Lang::txt('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE');
		}

		$pop   = $pop ? '<p class="warning">' . $pop . '</p>' : '';

		$html = '';

		// Which role?
		$role = $element->params->role;

		$url = Route::url('index.php?option=com_publications&task=serve&id='
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
			$label = Lang::txt('COM_PUBLICATIONS_LAUNCH_TOOL');
			$class = 'btn btn-primary active icon-next';
			$class .= $disabled ? ' link_disabled' : '';
			$title = $configs->title ? $configs->title : Lang::txt('COM_PUBLICATIONS_LAUNCH_TOOL');
			$html  = \Components\Publications\Helpers\Html::primaryButton($class, $url, $label, null,
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
		// Loop through attachments
		foreach ($attachments as $att)
		{
			// Make new attachment record
			$pAttach = new \Components\Publications\Tables\Attachment( $this->_parent->_db );
			if (!$pAttach->copyAttachment($att, $newVersion->id, $elementId, User::get('id') ))
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
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : null;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments($elementId, $attachments, $this->_name);

		// Default to first attachment
		$record = count($attachments) > 0 ? $attachments[0] : null;

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
			$this->setError( Lang::txt('Oops! Something went wrong. Cannot redirect to content.') );
			return false;
		}

		//get tool params
		$params = Component::params('com_tools');

		// Generate the URL that launches a tool session
		$v = $record->object_revision ? $record->object_revision : 'dev';
		$path = Route::url('index.php?option=com_tools&app=' . $record->object_name . '&task=invoke&version=' . $v);

		if (!$path)
		{
			$this->setError( Lang::txt('Oops! Something went wrong. Cannot redirect to content.') );
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
			$selections = Request::getVar( 'selecteditems', '');
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
			if (!Filesystem::makeDirectory( $configs->dataPath, 0755, true, true ))
			{
				$this->_parent->setError( Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_UNABLE_TO_CREATE_PATH') );
				return false;
			}
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

			if ($this->addAttachment($identifier, $pub, $configs, User::get('id'), $elementId, $element, $ordering))
			{
				$i++;
			}
		}

		// Success
		if ($i > 0 && $i == $a)
		{
			$message = $this->get('_message') ? $this->get('_message') : Lang::txt('Selection successfully saved');
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
		$title 	= Request::getVar( 'title', '' );
		$thumb 	= Request::getInt( 'makedefault', 0 );

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Update label
		$row->title 		= $title;
		$row->modified_by 	= User::get('id');
		$row->modified 		= Date::toSql();

		// Update record
		if (!$row->store())
		{
			$this->setError(Lang::txt('Error updating item record'));
		}

		$this->set('_message', Lang::txt('Update successful'));

		return true;
	}

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $element, $attachments )
	{
		$status = new \Components\Publications\Models\Status();

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
				$status->setError( Lang::txt('Need at least ' . $min . ' attachment') );
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
			$status->setError( Lang::txt('Maximum ' . $max . ' attachment(s) allowed') );
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
		$data->props	= $view->master->block . '-' . $view->master->blockId . '-' . $view->elementId;
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
