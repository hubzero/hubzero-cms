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
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

include_once __DIR__ . '/zone/location.php';

/**
 * Tool zone model
 *
 * @uses \Hubzero\Database\Relational
 */
class Zone extends Relational
{
	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'zone'   => 'notempty',
		'master' => 'notempty',
		'state'  => 'notempty'
	);

	/**
	 * Sets up additional custom rules
	 *
	 * @return  void
	 */
	public function setup()
	{
		$this->addRule('state', function($data)
		{
			$data['state'] = strtolower($data['state']);

			return (in_array($data['state'], array('up', 'down')) ? false : Lang::txt('Invalid state provided.'));
		});
	}

	/**
	 * Generates automatic zone field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticZone($data)
	{
		return preg_replace("/[^A-Za-z0-9\-\_\.]/", '', strtolower($data['zone']));
	}

	/**
	 * Generates automatic title field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTitle($data)
	{
		if (!isset($data['title']) || !$data['title'])
		{
			$data['title'] = $data['zone'];
		}
		return $data['title'];
	}

	/**
	 * Get a list of locations
	 *
	 * @return  object
	 */
	public function locations()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Zone\\Location', 'zone_id');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove comments
		foreach ($this->locations as $location)
		{
			if (!$location->destroy())
			{
				$this->addError($location->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
