<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Attachment;

use Components\Publications\Models\Attachment as Base;
use stdClass;
use Request;
use Route;
use Lang;
use Date;

/**
 * Handles a publication link attachment
 */
class Publication extends Base
{
	/**
	* Attachment type name
	*
	* @var  string
	*/
	protected $_name = 'publication';

	/**
	 * Unique attachment properties
	 *
	 * @var  array
	 */
	protected $_connector = array('path');

	/**
	 * Get configs
	 *
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @return  boolean
	 */
	public function getConfigs($element, $elementId, $pub, $blockParams)
	{
		$configs = new stdClass;
		$typeParams = $element->typeParams;

		// Allow changes in non-draft version?
		$configs->freeze = 0;

		if (isset($blockParams->published_editing)
		 && $blockParams->published_editing == 0
		 && ($pub->state == 1 || $pub->state == 5))
		{
			$configs->freeze = 1;
		}

		// Log path
		$configs->logPath = \Components\Publications\Helpers\Html::buildPubPath($pub->id, $pub->version_id, '', 'logs', 0);

		// replace current attachments?
		$configs->replace = Request::getInt('replace_current', 0, 'post');

		// Verify file type against allowed before attaching?
		$configs->check = isset($blockParams->verify_types) ? $blockParams->verify_types : 0;

		// Get default title
		$title = isset($element->title) ? str_replace('{pubtitle}', $pub->title, $element->title) : NULL;
		$configs->title = str_replace('{pubversion}', $pub->version_label, $title);

		// Fancy launcher?
		$configs->fancyLauncher = isset($typeParams->fancyLauncher) ? $typeParams->fancyLauncher : 0;

		return $configs;
	}

	/**
	 * Draw list
	 *
	 * @param   array    $attachments
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   boolean  $authorized
	 * @return  string   HTML
	 */
	public function drawList($attachments, $element, $elementId, $pub, $blockParams, $authorized)
	{
		// Get configs
		$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

		$html = '';

		if ($attachments)
		{
			// Serve individually
			foreach ($attachments as $attach)
			{
				$itemUrl = Route::url('index.php?option=com_publications&id=' . $attach->object_id);

				$publication = new \Components\Publications\Models\Publication($attach->object_id, 'default');

				$title = $publication->title ? $publication->title : $configs->title;
				$title = $title ? $title : $attach->path;

				$description = '';
				if ($publication->get('abstract'))
				{
					$description = \Hubzero\Utility\String::truncate(stripslashes($publication->get('abstract')), 300) . "\n";
				}
				else if ($publication->get('description'))
				{
					$description = \Hubzero\Utility\String::truncate(stripslashes($publication->get('description')), 300) . "\n";
				}

				$pop = Lang::txt('View link') . ' ' . $title;

				$html .= '<li>';
				$html .= $authorized === 'administrator' ? '[' . $this->_name . '] ' : '';
				$html .= '<p><a href="' . $itemUrl . '" title="' . $pop . '" target="_blank" class="link-type">' . $title . '</a></p>';
				$html .= '<p>' . $description . '</p>';
				$html .= '</li>';
			}
		}

		return $html;
	}

	/**
	 * Draw launcher
	 *
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   array    $elements
	 * @param   boolean  $authorized
	 * @return  string   HTML
	 */
	public function drawLauncher($element, $elementId, $pub, $blockParams, $elements, $authorized)
	{
		// Get configs
		/*$configs = $this->getConfigs($element->params, $elementId, $pub, $blockParams);

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
		$pop = NULL;

		if ($pub->isUnpublished() || $pub->isDown())
		{
			$pop = Lang::txt('COM_PUBLICATIONS_STATE_UNPUBLISHED_POP');
			$disabled = 1;
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

		$pop = $pop ? '<p class="warning">' . $pop . '</p>' : '';

		$html = '';

		// Which role?
		$role = $element->params->role;

		$url = Route::url('index.php?option=com_publications&id=' . $pub->id . '&v=' . $pub->version_number);

		// Primary button
		if ($role == 1)
		{
			$attach = $attachments[0];

			if (count($attachments) > 1)
			{
				// TBD
			}

			// One launcher for all files
			$label = Lang::txt('View publication');

			$class  = 'btn btn-primary active icon-next';
			$class .= $disabled ? ' link_disabled' : '';

			$title = $configs->title ? $configs->title : Lang::txt('View publication');

			$html  = \Components\Publications\Helpers\Html::primaryButton($class, $url, $label, NULL, $title, 'rel="external"', $disabled, $pop);
		}
		elseif ($role == 2 && $attachments)
		{
			$html .= '<ul>';
			$html .= self::drawList($attachments, $element, $elementId, $pub, $blockParams, $authorized);
			$html .= '</ul>';
		}*/
		$html = '';

		return $html;
	}

	/**
	 * Transfer files from one version to another
	 *
	 * @param   object   $elementparams
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   array    $attachments
	 * @param   object   $oldVersion
	 * @param   object   $newVersion
	 * @return  void
	 */
	public function transferData($elementparams, $elementId, $pub, $blockParams, $attachments, $oldVersion, $newVersion)
	{
		// Loop through attachments
		foreach ($attachments as $att)
		{
			// Make new attachment record
			$pAttach = new \Components\Publications\Tables\Attachment($this->_parent->_db);

			if (!$pAttach->copyAttachment($att, $newVersion->id, $elementId, User::get('id')))
			{
				continue;
			}
		}
	}

	/**
	 * Serve
	 *
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   integer  $itemId
	 * @return  boolean
	 */
	public function serve($element, $elementId, $pub, $blockParams, $itemId = 0)
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
			$this->setError(Lang::txt('Oups! Something went wrong. Cannot redirect to content.'));
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
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   array    $toAttach
	 * @return  boolean
	 */
	public function save($element, $elementId, $pub, $blockParams, $toAttach = array())
	{
		if (empty($toAttach))
		{
			$selections = Request::getVar('selecteditems', '');
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
		if (empty($toAttach) && !$configs->replace)
		{
			return false;
		}

		// Get existing attachments for the elemnt
		$attachments = $pub->_attachments;
		$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;

		// Sort out attachments for this element
		$attachments = $this->_parent->getElementAttachments($elementId, $attachments, $this->_name);

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

			$row = new \Components\Publications\Tables\Version($this->_parent->_db);
			if (!$row->loadVersion($identifier, 'current'))
			{
				$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_PUBLICATION_NOT_FOUND'));
				return false;
			}

			$title = $row->title;
			$desc  = strip_tags($row->description);

			if ($this->addAttachment($identifier, $title, $pub, $configs, User::get('id'), $elementId, $element, $ordering))
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
	 * Add/edit file attachment
	 *
	 * @param   integer  $id
	 * @param   string   $title
	 * @param   object   $pub
	 * @param   object   $configs
	 * @param   integer  $uid
	 * @param   integer  $elementId
	 * @param   object   $element
	 * @param   integer  $ordering
	 * @return  boolean
	 */
	public function addAttachment($id, $title, $pub, $configs, $uid, $elementId, $element, $ordering = 1)
	{
		// Need to check against allowed types
		$accept = isset($element->typeParams->accept) ? $element->typeParams->accept : NULL;

		if ($configs->check)
		{
			if (!$this->checkAllowed($id, $accept))
			{
				return false;
			}
		}

		$path = rtrim(Request::base(), '/') . '/' . ltrim(Route::url('index.php?option=com_publications&id=' . $id), '/');

		$objPA = new \Components\Publications\Tables\Attachment($this->_parent->_db);

		if ($objPA->loadElementAttachment($pub->version_id, array('path' => $path), $elementId, $this->_name, $element->role))
		{
			// Link already attached
			$this->setError(Lang::txt('The publication is already attached'));
			return true;
		}
		else
		{
			$objPA->publication_id         = $pub->id;
			$objPA->publication_version_id = $pub->version_id;
			$objPA->path        = $path;
			$objPA->type        = $this->_name;
			$objPA->created_by  = $uid;
			$objPA->created     = Date::toSql();
			$objPA->role        = $element->role;
			$objPA->title       = $title;
			$objPA->object_id   = $id;
			$objPA->object_name = $this->_name;

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);
		}

		$objPA->element_id = $elementId;
		$objPA->ordering   = $ordering;

		if (!$objPA->store())
		{
			$this->setError(Lang::txt('There was a problem attaching the publication'));
			return false;
		}

		return true;
	}

	/**
	 * Remove attachment
	 *
	 * @return  boolean
	 */
	public function removeAttachment($row, $element, $elementId, $pub, $blockParams)
	{
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

			$this->set('_message', Lang::txt('Item removed'));

			// Reflect the update in curation record
			$this->_parent->set('_update', 1);

			return true;
		}

		return false;
	}

	/**
	 * Update attachment properties
	 *
	 * @return  boolean
	 */
	public function updateAttachment($row, $element, $elementId, $pub, $blockParams)
	{
		// Incoming
		$title = Request::getVar('title', '');
		$thumb = Request::getInt('makedefault', 0);

		// Get configs
		$configs = $this->getConfigs($element, $elementId, $pub, $blockParams);

		// Cannot make changes
		if ($configs->freeze)
		{
			return false;
		}

		// Update label
		$row->title       = $title;
		$row->modified_by = User::get('id');
		$row->modified    = Date::toSql();

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
	 * @param   object  $element
	 * @param   array   $attachments
	 * @return  object
	 */
	public function getStatus($element, $attachments)
	{
		$status = new \Components\Publications\Models\Status();

		// Get requirements to check against
		$max      = $element->max;
		$min      = $element->min;
		$role     = $element->role;
		$params   = $element->typeParams;
		$required = $element->required;
		$counter  = count($attachments);
		$allowed  = isset($params->accept) ? $params->accept :  NULL;

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
				$status->setError(Lang::txt('Need at least ' . $min . ' attachment'));
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
			$status->setError(Lang::txt('Maximum ' . $max . ' attachment(s) allowed'));
		}
		// Check allowed formats
		elseif (!self::checkAllowed($attachments, $allowed))
		{
			if ($counter && !empty($accept))
			{
				$error = Lang::txt('Error: unacceptable URL. URL should start with: ');
				foreach ($params->allowed_ext as $ext)
				{
					$error .= ' ' . $ext .',';
				}
				$error = substr($error, 0, strlen($error) - 1);
				$status->setError($error);
			}
		}

		$status->status = $status->getError() ? 0 : 1;

		return $status;
	}

	/**
	 * Check for allowed formats
	 *
	 * @param   array    $attachments
	 * @param   array    $formats
	 * @return  boolean
	 */
	public function checkAllowed($attachments, $formats = array())
	{
		if (empty($formats))
		{
			return true;
		}

		$attachments = (array)$attachments;

		foreach ($attachments as $attach)
		{
			$id = isset($attach->id) ? $attach->id : $attach;

			foreach ($formats as $f)
			{
				if (stripos($id, $f) !== false)
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
	 * @param   object  $data
	 * @param   object  $params
	 * @return  string
	 */
	public function drawAttachment($data, $params)
	{
		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'publications',
				'name'    =>'attachments',
				'layout'  => $this->_name
			)
		);
		$view->data   = $data;
		$view->params = $params;

		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Build Data object
	 *
	 * @param   object   $att
	 * @param   object   $view
	 * @param   integer  $i
	 * @return  object
	 */
	public function buildDataObject($att, $view, $i = 1)
	{
		$data = new stdClass;
		$data->row      = $att;
		$data->ordering = $i;
		$data->editUrl  = $view->pub->link('editversion');
		$data->id       = $att->id;
		$data->props    = $view->master->block . '-' . $view->master->blockId . '-' . $view->elementId;
		$data->viewer   = $view->viewer;
		$data->version  = $view->pub->version_number;

		return $data;
	}

	/**
	 * Add to zip bundle
	 *
	 * @param   object   $zip
	 * @param   array    $attachments
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   object   $readme
	 * @param   string   $bundleDir
	 * @return  boolean
	 */
	public function addToBundle($zip, $attachments, $element, $elementId, $pub, $blockParams, &$readme, $bundleDir)
	{
		return false;
	}

	/**
	 * Draw list
	 *
	 * @param   array    $attachments
	 * @param   object   $element
	 * @param   integer  $elementId
	 * @param   object   $pub
	 * @param   object   $blockParams
	 * @param   boolean  $authorized
	 * @return  boolean
	 */
	public function drawPackageList($attachments, $element, $elementId, $pub, $blockParams, $authorized)
	{
		return false;
	}
}