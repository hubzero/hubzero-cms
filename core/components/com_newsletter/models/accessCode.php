<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2020 The Regents of the University of California.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;

class AccessCode extends Relational
{
	protected $table = '#__reply_access_codes';

	public $initiate = ['created'];
}
