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
 * @author    Steve Snyder <snyder13@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;

include_once(dirname(__DIR__) . DS . 'result.php');

/**
 * Associative scalar result
 */
class AssocScalar extends SearchResult
{
	/**
	 * Description for 'tag_weight_modifier'
	 *
	 * @var number
	 */
	private static $tag_weight_modifier;

	/**
	 * Description for 'row'
	 *
	 * @var unknown
	 */
	private $row;

	/**
	 * Short description for 'is_scalar'
	 *
	 * Long description (if any) ...
	 *
	 * @return     boolean Return description (if any) ...
	 */
	public function is_scalar()
	{
		return true;
	}

	/**
	 * Short description for 'assert_keys'
	 *
	 * Long description (if any) ...
	 *
	 * @param   array    $keys Parameter description (if any) ...
	 * @param   unknown  $row  Parameter description (if any) ...
	 * @return  void
	 * @throws  Exception
	 */
	private static function assert_keys($keys, $row)
	{
		foreach ($keys as $key)
		{
			if (!array_key_exists($key, $row))
			{
				throw new Exception("Result plugin did not define key '$key'");
			}
		}
	}

	/**
	 * Short description for '__construct'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $row Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct($row)
	{
		if (is_null(self::$tag_weight_modifier))
		{
			self::$tag_weight_modifier = Set::get_tag_weight_modifier();
		}

		self::assert_keys(array('title', 'description', 'link'), $row);
		foreach ($row as $key=>$val)
		{
			$this->$key = is_array($val) ? array_map('stripslashes', array_map('strip_tags', $val)) : stripslashes(strip_tags($val));
		}

		if ($this->weight === NULL)
		{
			if ($this->tag_count)
			{
				$this->weight = $this->tag_count * (self::$tag_weight_modifier / 2);
			}
			$this->weight = 1.0;
			$this->weight_log[] = 'plugin did not suggest weight, guessing '.$this->weight.' based on tag count('.$this->tag_count.')';
		}
		else if ($this->tag_count)
		{
			$this->weight_log[] = 'plugin suggested weight of '.$this->weight;
			$this->adjust_weight($this->tag_count * self::$tag_weight_modifier, 'tag count of '.$this->tag_count);
		}

		$this->contributors = $this->contributors ? array_unique(is_array($this->contributors) ? $this->contributors : preg_split("#\n#", $row['contributors'])) : array();
		$this->contributor_ids = $this->contributor_ids ? array_unique(is_array($this->contributor_ids) ? $this->contributor_ids : preg_split("#\n#", $row['contributor_ids'])) : array();

		if ($this->date && $this->date != '0000-00-00 00:00:00')
		{
			$this->date = strtotime($row['date']);
		}
		else
		{
			$this->date = null;
		}
	}

	/**
	 * Short description for 'get_result'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function get_result()
	{
		return $this->row;
	}

	/**
	 * Short description for 'to_associative'
	 *
	 * Long description (if any) ...
	 *
	 * @return     unknown Return description (if any) ...
	 */
	public function to_associative()
	{
		return $this;
	}
}
