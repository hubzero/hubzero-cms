<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author	Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 * @since	 Class available since release 1.3.2
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Base\Object;

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Author extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'citations';
	// table name jos_citations

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'name';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		//'name'	=> 'notempty',
		//'liaison' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		//'name_normalized',
		//'asset_id'
	);


	/**
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 * @author
	 **/
	public function citation()
	{
		return $this->belongsToOne('Citation', 'cid', 'id');
	}
}
