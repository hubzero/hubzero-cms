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
 * Renders metadata element
 */
class PublicationsModelBlockElementMetadata extends PublicationsModelBlockElement
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
		$status = new PublicationsModelStatus();

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
				$data[$match[1]] = PublicationsHtml::_txtUnpee($match[2]);
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
				$status->setError( JText::_('Default value needs to be replaced') );
			}
		}
		// Required value not filled?
		if ($required && !$value)
		{
			$status->setError( JText::_('Please enter ' . $key) );
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
		$activeElement  = JRequest::getInt( 'el', $showElement );

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