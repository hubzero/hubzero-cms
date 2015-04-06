<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

use Components\Billboards\Models\Collection;
use Components\Billboards\Models\Billboard;

JLoader::import('Hubzero.Api.Controller');

require_once PATH_CORE . DS . 'components' . DS . 'com_billboards' . DS . 'models' . DS . 'collection.php';
require_once PATH_CORE . DS . 'components' . DS . 'com_billboards' . DS . 'models' . DS . 'billboard.php';

/**
 * Billboards api controller
 */
class BillboardsControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Default execute task, handling task execution
	 *
	 * @return void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'index':       $this->index();      break;
			case 'collection':  $this->collection(); break;
			default:            $this->index();
		}
	}

	/**
	 * Default not found task, throwing a 404
	 *
	 * @return void
	 */
	private function not_found()
	{
		$response = $this->getResponse();
		$response->setErrorMessage(404, 'Not Found');
	}

	/**
	 * Lists an index of existing billboard collections
	 *
	 * @return void
	 */
	private function index()
	{
		// If we dont have a user, return an error
		if (JFactory::getApplication()->getAuthn('user_id') == null)
		{
			return $this->not_found();
		}

		// Get the request vars
		$limit      = Request::getVar("limit", 25);
		$limitstart = Request::getVar("limitstart", 0);

		// Load up the entries
		$collections = Collection::start($limitstart)->limit($limit)->rows();

		$this->setMessageType("application/json");
		$this->setMessage(array('collections' => $collections->toArray()));
	}

	/**
	 * Lists all billboards for a given collection
	 *
	 * @return void
	 */
	private function collection()
	{
		// If we dont have a user, return an error
		if (JFactory::getApplication()->getAuthn('user_id') == null)
		{
			return $this->not_found();
		}

		// Get the collection id
		$collection = 0;
		if (isset($this->segments[1]))
		{
			$collection = $this->segments[1];
		}
		$collection = Request::getVar("collection", $collection);

		// Load up the collection
		$collection = Collection::oneOrNew($collection);

		// Make sure we found a collection
		if ($collection->isNew())
		{
			return $this->not_found();
		}

		$billboards = $collection->billboards()
		                         ->select('name')
		                         ->select('learn_more_target')
		                         ->select('background_img')
		                         ->whereEquals('published', 1)
		                         ->rows();

		foreach ($billboards as $billboard)
		{
			$image = $billboard->get('background_img');
			$billboard->set('retina_background_img', $image);

			if (is_file(PATH_APP . DS . $image))
			{
				$image_info   = pathinfo($image);
				$retina_image = $image_info['dirname'] . DS . $image_info['filename'] . "@2x." . $image_info['extension'];
				if (file_exists(PATH_APP . DS . $retina_image))
				{
					$billboard->set('retina_background_img', $retina_image);
				}
			}
		}

		// Get the collection and its billboards
		$collection = array(
			'id'         => $collection->id,
			'name'       => $collection->name,
			'billboards' => $billboards->toArray()
		);

		$this->setMessageType("application/json");
		$this->setMessage(array('collection' => $collection));
	}
}
