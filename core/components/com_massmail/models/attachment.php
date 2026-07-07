<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Models;

use Hubzero\Database\Relational;

/**
 * Mass mail: a file attached to a mailing.
 */
class Attachment extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'massmail';

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'mailing_id' => 'positive|nonzero',
		'filename'   => 'notempty'
	);

	/**
	 * The mailing this attachment belongs to
	 *
	 * @return  object
	 */
	public function mailing()
	{
		return $this->belongsToOne('Mailing', 'mailing_id');
	}
}
