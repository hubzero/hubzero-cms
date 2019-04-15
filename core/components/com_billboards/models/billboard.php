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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Billboards\Models;

use Hubzero\Database\Relational;

/**
 * Billboard database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Billboard extends Relational
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

		return $result ? $result : 1;
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
		return $this->belongsToOne(__NAMESPACE__ . '\\Collection');
	}

	/**
	 * Gets the background image url
	 *
	 * @return string
	 * @since  1.3.2
	 */
	public function transformBackgroundImg()
	{
		$params = Component::params('com_billboards');
		$base   = $params->get('image_location', DS . 'app' . DS . 'site' . DS . 'media' . DS . 'images' . DS . 'billboards' . DS);

		return DS . trim($base, DS) . DS . $this->get('background_img');
	}
}
