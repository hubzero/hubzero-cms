<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
			'domain_id' => $page->get('scope_id'),
			'url'       => $page->link(),
			'loglinks'  => true
		);

		$parser = Parser::getInstance();

		// Parse the text
		return $parser->parse($this->get('pagetext'), $wikiconfig, true, true);
	}
}
