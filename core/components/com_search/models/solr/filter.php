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
 * @since     2.1.4
 */

namespace Components\Search\Models\Solr;
require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'option.php';

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Database model for search blacklist
 *
 * @uses  \Hubzero\Database\Relational
 */
class Filter extends Relational
{
	/**
	 * Table name
	 * 
	 * @var  string
	 */
	protected $table = '#__solr_search_filters';

	/**
	 * Automatic fields to populate every time a row is updated
	 *
	 * @var  array
	 */
	public $always = array(
		'params',
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * children 
	 * 
	 * @return  object
	 */
	public function options()
	{
		return $this->oneToMany('Option');
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!isset($this->params))
		{
			$params = new Registry($this->get('params'));
			$this->params = $params;
		}
		return $this->params;
	}

	/**
	 * Make sure params are a string
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticParams($data)
	{
		if (!empty($data['params']))
		{
			if (!is_string($data['params']))
			{
				if (!($data['params'] instanceof Registry))
				{
					$data['params'] = new Registry($data['params']);
				}
				$data['params'] = $data['params']->toString();
			}
			return $data['params'];
		}
	}

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		$data['modified'] = Date::of()->toSql();
		return $data['modified'];
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModifiedBy($data)
	{
		$data['modified_by'] = User::getInstance()->get('id');
		return $data['modified_by'];
	}

	/**
	 * Convert facet name to solr query safe name
	 *
	 * @return  string  name of query
	 */
	public function getQueryName()
	{
		$name = str_replace(' ', '_', $this->field);
		return $name;
	}

}
