<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'rule.php';
require_once __DIR__ . DS . 'tool.php';

/**
 * Tool file handlers database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Handler extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'tool';

	/**
	 * Default order dir for fetch
	 *
	 * @var string
	 **/
	public $orderBy = 'tool.title';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'tool_id' => 'notempty|nonzero',
		'prompt'  => 'notempty'
	);

	/**
	 * Defines the relationship between a handler and it's rules
	 *
	 * @return \Hubzero\Database\Relationship\oneToMany
	 **/
	public function rules()
	{
		return $this->oneToMany('Rule');
	}

	/**
	 * Defines the inverse relationship between a handler and a tool
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 **/
	public function tool()
	{
		return $this->belongsToOne('Tool');
	}

	/**
	 * Returns the known handlers for a given file
	 *
	 * @return  array|bool
	 **/
	public static function getLaunchUrlsForFile($filename)
	{
		// Figure out the extension of interest
		$bits      = explode('.', $filename);
		$extension = array_pop($bits);
		$results   = [];

		// First, limit handlers to only those with 1 rule
		$handlers = static::all()->whereRelatedHasCount('rules', 1, 0, '=');

		// Now, make sure that rule is for this file extension and only has a quantity of 1
		$rows = $handlers->whereRelatedHas('rules', function ($rules) use ($extension)
		{
			$rules->whereEquals('extension', $extension)
			      ->whereEquals('quantity', 1);
		})->rows();

		if ($rows->count() == 0)
		{
			return false;
		}
		else
		{
			foreach ($rows as $row)
			{
				$results[] = [
					'prompt' => $row->prompt,
					'url'    => '/tools/' . $row->tool->toolname . '/invoke?params=file:' . urlencode($filename)
				];
			}
		}

		return $results;
	}
}
