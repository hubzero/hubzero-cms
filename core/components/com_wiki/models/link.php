<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;
use Date;
use Lang;

/**
 * Wiki model for page URIs
 */
class Link extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'page_id' => 'positive|nonzero',
		'scope'   => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'timestamp'
	);

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  string
	 */
	public function automaticTimestamp($data)
	{
		if (!isset($data['timestamp']))
		{
			$data['timestamp'] = Date::of('now')->toSql();
		}
		return $data['timestamp'];
	}

	/**
	 * Defines a belongs to one relationship between task and liaison
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
			return Date::of($this->get('timestamp'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($this->get('timestamp'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($format)
		{
			return Date::of($this->get('timestamp'))->toLocal($format);
		}

		return $this->get('timestamp');
	}

	/**
	 * Delete all entries for a specific page
	 *
	 * @param   array    $links
	 * @return  boolean  True on success
	 */
	public function addLinks($links=array())
	{
		if (count($links) <= 0)
		{
			return true;
		}

		$timestamp = Date::toSql();

		foreach ($links as $link)
		{
			if (!$link['page_id'])
			{
				$this->addError('Missing page ID for link "%s"', $link['link']);
				continue;
			}
			$row = self::blank();
			$row->set('page_id', intval($link['page_id']));
			$row->set('timestamp', $timestamp);
			$row->set('scope', $link['scope']);
			$row->set('scope_id', intval($link['scope_id']));
			$row->set('link', $link['link']);
			$row->set('url', $link['url']);

			if (!$row->save())
			{
				$this->addError($row->getError());
				return false;
			}
		}

		return true;
	}

	/**
	 * Update entries
	 *
	 * @param   integer  $page_id  Page ID
	 * @param   array    $data
	 * @return  boolean  True on success
	 */
	public function updateLinks($page_id, $data=array())
	{
		$links = array();

		foreach ($data as $data)
		{
			// Eliminate duplicates
			$links[$data['link']] = $data;
		}

		$rows = self::all()
			->whereEquals('page_id', $page_id)
			->rows();

		foreach ($rows as $row)
		{
			if (!isset($links[$row->get('link')]))
			{
				// Link wasn't found, delete it
				$row->destroy();
			}
			else
			{
				unset($links[$row->get('link')]);
			}
		}

		if (count($links) > 0)
		{
			return $this->addLinks($links);
		}

		return true;
	}
}
