<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Billboard database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Billboard extends \Hubzero\Database\Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'billboards';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'name' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $initiate = array(
		'ordering'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		'alias'
	);

	/**
	 * Generates automatic ordering field
	 *
	 * @return int
	 * @since  1.3.2
	 **/
	public function automaticOrdering()
	{
		$instance = self::blank();
		$result = $instance->select('MAX(ordering) + 1', 'ordering')
		                   ->whereEquals('collection_id', $this->collection_id)
		                   ->rows()
		                   ->first()
		                   ->ordering;

		return $result;
	}

	/**
	 * Generates automatic alias field
	 *
	 * @return string
	 * @since  1.3.2
	 **/
	public function automaticAlias()
	{
		return strtolower(preg_replace("/[^[:alnum:]]/ui", "", (($this->alias) ? $this->alias : $this->name)));
	}

	/**
	 * Defines a belongs to one relationship with collection
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function collection()
	{
		return $this->belongsToOne('Collection');
	}

	/**
	 * Gets the background image url
	 *
	 * @return string
	 * @since  1.3.2
	 */
	public function transformBackgroundImg()
	{
		$params = \JComponentHelper::getParams('com_billboards');
		$base   = $params->get('image_location', DS . 'site' . DS . 'media' . DS . 'images' . DS . 'billboards' . DS);

		return DS . trim($base, DS) . DS . $this->get('background_img');
	}
}