<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Projects microblog model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Microblog extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project';

	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $table = '#__project_microblog';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'projectid'  => 'positive|nonzero',
		'activityid' => 'positive|nonzero',
		'blogentry'  => 'notempty'
	);
}
