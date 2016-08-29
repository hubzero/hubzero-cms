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

namespace Components\Wiki\Models;

use Components\Wiki\Helpers\Parser;
use Hubzero\Database\Relational;
use Lang;
use Date;

require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Wiki model for a page version
 */
class Version extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Adapter type
	 *
	 * @var  object
	 */
	protected $adapter = null;

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'page_id'  => 'positive|nonzero',
		'version'  => 'positive',
		'pagetext' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'version',
		'length'
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
	 * Generates automatic owned by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 */
	public function automaticVersion($data)
	{
		if (!isset($data['version']) || $data['version'] <= 0)
		{
			$data['version'] = 1;
		}
		return $data['version'];
	}

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 */
	public function automaticLength($data)
	{
		$data['length'] = strlen($data['pagetext']);
		return $data['length'];
	}

	/**
	 * Does the page exist?
	 *
	 * @return  boolean
	 */
	public function exists()
	{
		return ! $this->isNew();
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Defines a belongs to one relationship to the parent page
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Page', 'page_id');
	}

	/**
	 * Return a formatted timestamp for created date
	 *
	 * @param   string  $as
	 * @param   string  $format
	 * @return  string
	 */
	public function created($as='', $format=null)
	{
		$as = strtolower($as);

		if ($as == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($format)
		{
			return Date::of($this->get('created'))->toLocal($format);
		}

		return $this->get('created');
	}

	/**
	 * Get the content of the record.
	 * Optional argument to determine how content should be handled
	 *
	 * parsed - performs parsing on content (i.e., converting wiki markup to HTML)
	 * clean  - parses content and then strips tags
	 * raw    - as is, no parsing
	 *
	 * @param   object  $page
	 * @param   string  $option
	 * @return  string
	 */
	public function content($page=null, $option=null)
	{
		if (!$page)
		{
			$page = $this->page;
		}

		$route = $page->adapter()->routing('');
		$route['option'] = '';
		$route = implode('/', $route);
		$route = ($route ? $route . '/' : $route);

		$wikiconfig = array(
			'option'    => ($option ?: \Request::getCmd('option')),
			'scope'     => $page->get('path'), // $route . $page->get('path'),
			'pagename'  => $page->get('pagename'),
			'pageid'    => $page->get('id'),
			'filepath'  => '',
			'domain'    => $page->get('scope'),
			'domain_id' => $page->get('scope_id')
		);

		$parser = Parser::getInstance();

		// Parse the text
		return $parser->parse($this->get('pagetext'), $wikiconfig, true, true);
	}
}
