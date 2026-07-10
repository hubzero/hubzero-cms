<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Models;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'recipient.php';
require_once __DIR__ . DS . 'attachment.php';

/**
 * Mass mail: a single mailing (one composed message + its recipient queue).
 */
class Mailing extends Relational
{
	const STATUS_DRAFT            = 'draft';
	const STATUS_PENDING_APPROVAL = 'pending_approval';
	const STATUS_HELD             = 'held';
	const STATUS_SCHEDULED        = 'scheduled';
	const STATUS_PREPARING        = 'preparing';
	const STATUS_SENDING          = 'sending';
	const STATUS_PAUSED           = 'paused';
	const STATUS_SENT             = 'sent';
	const STATUS_CANCELLED        = 'cancelled';

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'massmail';

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
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'subject' => 'notempty'
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
	 * Recipients for this mailing
	 *
	 * @return  object
	 */
	public function recipients()
	{
		return $this->oneToMany('Recipient', 'mailing_id');
	}

	/**
	 * Attachments for this mailing
	 *
	 * @return  object
	 */
	public function attachments()
	{
		return $this->oneToMany('Attachment', 'mailing_id');
	}

	/**
	 * Creator of this mailing
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Count recipients in a given status
	 *
	 * @param   integer  $status
	 * @return  integer
	 */
	public function countRecipients($status = null)
	{
		$rows = $this->recipients();
		if (!is_null($status))
		{
			$rows->whereEquals('status', $status);
		}
		return $rows->total();
	}

	/**
	 * Is this mailing still actively being delivered?
	 *
	 * @return  boolean
	 */
	public function isActive()
	{
		return in_array($this->get('status'), array(
			self::STATUS_PREPARING,
			self::STATUS_SENDING
		));
	}

	/**
	 * Human-readable list of selectable statuses
	 *
	 * @return  array
	 */
	public static function statuses()
	{
		return array(
			self::STATUS_DRAFT,
			self::STATUS_PENDING_APPROVAL,
			self::STATUS_HELD,
			self::STATUS_SCHEDULED,
			self::STATUS_PREPARING,
			self::STATUS_SENDING,
			self::STATUS_PAUSED,
			self::STATUS_SENT,
			self::STATUS_CANCELLED
		);
	}
}
