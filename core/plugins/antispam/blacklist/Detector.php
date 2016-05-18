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

use Plugins\Antispam\BlackList\Models\Word;
use Hubzero\Spam\Detector\DetectorInterface;
use Hubzero\Database\Relational;
use Exception;

include_once(__DIR__ . DS . 'models' . DS . 'word.php');

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
	 * Model for retrieving list of words
	 *
	 * @var  object
	 */
	protected $model = null;

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

		if (!isset($options['model']))
		{
			$options['model'] = Word::blank();
		}

		$this->setModel($options['model']);

		$this->message = '';
	}

	/**
	 * Adds a word/pattern to the black list.
	 * Set the second argument to true to treat
	 * the added word as a regular expression.
	 *
	 * @param   string  $vars   List of blacklisted words
	 * @param   bool    $regex  Flags word as regex pattern
	 * @return  object
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
	 * Set model
	 *
	 * @param   object  $model
	 * @return  object
	 * @throws  Exception
	 */
	public function setModel($model)
	{
		if (!($model instanceof Relational))
		{
			throw new Exception('Model must extend the Hubzero\\Database\\Relational');
		}

		$this->model = $model;

		return $this;
	}

	/**
	 * Get the model
	 *
	 * @return  object
	 */
	public function getModel()
	{
		return $this->model;
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

			if ($storage = $this->getModel())
			{
				$rows = $storage->rows();

				if ($rows->count())
				{
					foreach ($rows as $row)
					{
						$dbList[] = $row->get('word');
					}
				}
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
