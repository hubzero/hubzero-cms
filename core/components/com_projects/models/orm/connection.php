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

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Filesystem\Manager;
use User;

include_once __DIR__ . '/provider.php';

/**
 * Connections model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Connection extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'projects';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'project_id'  => 'positive|nonzero',
		'provider_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between connections and connection providers
	 *
	 * @return  \Hubzero\Database\Relationship\BelongsToOne
	 **/
	public function provider()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Provider', 'provider_id');
	}

	/**
	 * Generates the filesystem adapter for the given provider
	 *
	 * @param   array   $options  extra params to include with defaults
	 * @return  object
	 **/
	public function adapter($options=[])
	{
		$params = (array)json_decode($this->params);
		$params = array_merge($params, $options);

		return Manager::adapter($this->provider->alias, $params);
	}

	/**
	 * Gets the connection name, defaulting to the provider name if not set
	 *
	 * @return  string
	 **/
	public function transformName()
	{
		if ($this->hasAttribute('name'))
		{
			return $this->get('name');
		}

		return $this->provider->name;
	}

	/**
	 * Gets the connections that are mine or are public to my project
	 *
	 * @return  $this
	 **/
	public function thatICanView()
	{
		return $this->whereEquals('owner_id', User::get('id'), 1)
			->orWhereEquals('owner_id', 0, 1)
			->orWhereRaw('owner_id IS NULL', [], 1);
	}

	/**
	 * Checks to see if a given connection is shared or private
	 *
	 * @return  bool
	 **/
	public function isShared()
	{
		return !$this->owner_id ? true : false;
	}
}