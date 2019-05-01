<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public function getStatus( $manifest, $pub = null )
	{
		$status = new \Components\Publications\Models\Status();

		// Get requirements to check against
		$field	  = $manifest->params->field;
		$required = $manifest->params->required;
		$key 	  = $manifest->params->aliasmap;
		$default  = isset($manifest->params->default) ? $manifest->params->default : null;
		$value	  = isset($pub->$key) ? $pub->$key : null;

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
			$value = isset($data[$key]) ? $data[$key] : null;
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
	public function render( $elementid, $manifest, $pub = null, $viewname = 'edit',
		$status = null, $master = null, $order = 0 )
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
	public function drawItem( $elementId, $manifest, $pub = null,
		$status = null, $master = null, $viewname = 'freeze')
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
	public function drawFormField( $elementId, $manifest, $pub = null,
		$status = null, $active = 0, $collapse = 0, $total = 0,
		$master = null, $order = 0)
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
