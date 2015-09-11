<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

namespace Components\Publications\Models\BlockElement;

use Components\Publications\Models\BlockElement as Base;

/**
 * Renders metadata element
 */
class Metadata extends Base
{
	/**
	* Element name
	*
	* @var		string
	*/
	protected	$_name = 'metadata';

	/**
	 * Check completion status
	 *
	 * @return  object
	 */
	public function getStatus( $manifest, $pub = NULL )
	{
		$status = new \Components\Publications\Models\Status();

		// Get requirements to check against
		$field	  = $manifest->params->field;
		$required = $manifest->params->required;
		$key 	  = $manifest->params->aliasmap;
		$default  = isset($manifest->params->default) ? $manifest->params->default : NULL;
		$value	  = isset($pub->$key) ? $pub->$key : NULL;

		$incomplete = 0;

		// Parse data in metadata field
		$data = array();
		preg_match_all("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", $pub->metadata, $matches, PREG_SET_ORDER);
		if (count($matches) > 0)
		{
			foreach ($matches as $match)
			{
				$data[$match[1]] = \Components\Publications\Helpers\Html::_txtUnpee($match[2]);
			}
		}

		// Metadata field (special treatment)
		if ($field == 'metadata')
		{
			$value = isset($data[$key]) ? $data[$key] : NULL;
		}

		// Default value not replaced?
		if ($default && $value)
		{
			if ($default == $value || preg_match('/' . $default . ' (\\(.*\\))/', $value, $matches))
			{
				$status->setError( Lang::txt('Default value needs to be replaced') );
			}
		}
		// Required value not filled?
		if ($required && !$value)
		{
			$status->setError( Lang::txt('Missing ' . $key) );
		}
		elseif (!$required && !$value)
		{
			$incomplete = 1;
		}

		$status->status = $status->getError() ? 0 : 1;
		$status->status = $incomplete ? 2 : $status->status;

		return $status;
	}

	/**
	 * Render
	 *
	 * @return  object
	 */
	public function render( $elementid, $manifest, $pub = NULL, $viewname = 'edit',
		$status = NULL, $master = NULL, $order = 0 )
	{
		$html   = '';

		$showElement 	= $master->props['showElement'];
		$total 			= $master->props['total'];

		// Incoming
		$activeElement  = Request::getInt( 'el', $showElement );

		// Do we need to collapse inactive elements?
		$collapse = isset($master->params->collapse_elements) && $master->params->collapse_elements ? 1 : 0;

		switch ($viewname)
		{
			case 'edit':
			default:
				$html = $this->drawFormField( $elementid, $manifest, $pub,
					$status->elements->$elementid, $activeElement, $collapse,
					$total, $master, $order);

			break;

			case 'freeze':
			case 'curator':
				$html = $this->drawItem( $elementid, $manifest, $pub, $status->elements->$elementid, $master, $viewname);
			break;
		}

		return $html;
	}

	/**
	 * Draw element with no editing capabilities
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
				'layout'	=>'metadata'
			)
		);

		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->name			 = $viewname;
		$view->master		 = $master;

		return $view->loadTemplate();
	}

	/**
	 * Draw element
	 *
	 * @return  object
	 */
	public function drawFormField( $elementId, $manifest, $pub = NULL,
		$status = NULL, $active = 0, $collapse = 0, $total = 0,
		$master = NULL, $order = 0)
	{
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'	=>'projects',
				'element'	=>'publications',
				'name'		=>'blockelement',
				'layout'	=>'metadata'
			)
		);

		$view->pub 			 = $pub;
		$view->manifest		 = $manifest;
		$view->status		 = $status;
		$view->elementId	 = $elementId;
		$view->active		 = $active;
		$view->collapse		 = $collapse;
		$view->total		 = $total;
		$view->master 		 = $master;
		$view->order		 = $order;

		return $view->loadTemplate();
	}
}