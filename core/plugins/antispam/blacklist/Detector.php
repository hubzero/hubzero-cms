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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\BlackList;

use Plugins\Antispam\BlackList\Table\Word;
use Hubzero\Spam\Detector\DetectorInterface;
use Exception;

include_once(__DIR__ . DS . 'Table' . DS . 'Word.php');

/**
 * Spam detector for black listed words
 */
class Detector implements DetectorInterface
{
	/**
	 * Regex for word detection
	 *
	 * @var  string
	 */
	protected $regex;

	/**
	 * Rebuild the regex?
	 *
	 * @var  bool
	 */
	protected $rebuild = false;

	/**
	 * Holds blacklisted words
	 *
	 * @var  array
	 */
	protected $blackLists = array();

	/**
	 * Holds the file that stores blacklisted words
	 *
	 * @var  null
	 */
	protected $db = null;

	/**
	 * Message
	 *
	 * @var  string
	 */
	protected $message = '';

	/**
	 * Constructor
	 *
	 * @param   array  $options
	 * @return  void
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['blackLists']))
		{
			$this->blackLists = $options['blackLists'];
		}

		if (!isset($options['db']))
		{
			$options['db'] = \App::get('db');
		}

		$this->setDbo($options['db']);

		$this->message = '';
	}

	/**
	 * Adds a word/pattern to the black list.
	 * Set the second argument to true to treat
	 * the added word as a regular expression.
	 *
	 * @param   string  $vars   List of blacklisted words
	 * @param   bool    $regex  Flags word as regex pattern
	 * @return  BlackList
	 */
	public function add($vars, $regex = false)
	{
		if (!is_array($vars))
		{
			$vars = array($vars);
		}

		foreach ($vars as $var)
		{
			$this->blackLists[] = $regex ? '[' . $var . ']' : $var;
		}

		return $this;
	}

	/**
	 * Set database connection
	 *
	 * @param   string  $file
	 * @return  object
	 * @throws  Exception
	 */
	public function setDbo($db)
	{
		if (!($db instanceof \Hubzero\Database\Driver))
		{
			throw new Exception('Database object must extend the Hubzero database driver');
		}

		$this->db = $db;

		return $this;
	}

	/**
	 * Get the database
	 *
	 * @return  object
	 */
	public function getDbo()
	{
		return $this->db;
	}

	/**
	 * Set the flag for rebuilding the regex
	 *
	 * @param   boolean  $flag
	 * @return  object
	 */
	public function rebuildRegex($flag)
	{
		$this->rebuild = $flag;

		return $this;
	}

	/**
	 * Checks the text if it contains any word that is blacklisted.
	 *
	 * @param   array  $data
	 * @return  bool
	 */
	public function detect($data)
	{
		// We only need the text from the data
		$text = $data['text'];

		if (!$this->regex || $this->rebuild)
		{
			$dbList = array();

			$tbl = new Word($this->getDbo());
			if ($tbl->getFields())
			{
				$dbList = $tbl->find('array');
			}

			$blackLists = array_merge($this->blackLists, $dbList);

			$this->regex = sprintf('~%s~', implode('|', array_map(function ($value)
			{
				if (isset($value[0]) && $value[0] == '[')
				{
					$value = substr($value, 1, -1);
				}
				else
				{
					$value = preg_quote($value);
				}

				return '(?:' . $value . ')';
			}, $blackLists)));
		}

		return (bool) preg_match($this->regex, $text);
	}

	/**
	 * {@inheritDocs}
	 */
	public function message()
	{
		return $this->message;
	}
}
