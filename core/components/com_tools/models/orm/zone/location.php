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
namespace Components\Tools\Models\Orm\Zone;

use Hubzero\Database\Relational;
use Lang;

/**
 * Tool zone location model
 *
 * @uses \Hubzero\Database\Relational
 */
class Location extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'zone';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = 'zone_locations';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'zone_id' => 'positive|nonzero',
		'ipFROM'  => 'notempty'
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
	 * Generates automatic ipFROM field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIpFrom($data)
	{
		if (strpos($data['ipFROM'], '/') !== false)
		{
			$cidr         = explode('/', $data['ipFROM']);
			$data['ipFROM'] = ip2long($cidr[0]) & ((-1 << (32 - (int)$cidr[1])));
			$data['ipTO']   = ip2long($cidr[0]) + pow(2, (32 - (int)$cidr[1])) - 1;
		}

		if (strstr($data['ipFROM'], '.'))
		{
			$data['ipFROM'] = ip2long($data['ipFROM']);
		}

		return $data['ipFROM'];
	}

	/**
	 * Generates automatic ipTO field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticIpTo($data)
	{
		if (strpos($data['ipFROM'], '/') !== false)
		{
			$cidr         = explode('/', $data['ipFROM']);
			$data['ipFROM'] = ip2long($cidr[0]) & ((-1 << (32 - (int)$cidr[1])));
			$data['ipTO']   = ip2long($cidr[0]) + pow(2, (32 - (int)$cidr[1])) - 1;
		}

		if (strstr($data['ipTO'], '.'))
		{
			$data['ipTO'] = ip2long($data['ipTO']);
		}

		return $data['ipTO'];
	}

	/**
	 * Defines a belongs to one relationship between location and zone
	 *
	 * @return  object
	 */
	public function zone()
	{
		return $this->belongsToOne('Components\Tools\Models\Orm\Zone', 'zone_id');
	}
}
