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
     * Automatic fields to populate every time a row is updated. The framework
     * runs these on every save of an existing record, so `modified` /
     * `modified_by` are stamped on any content or status change without each
     * caller having to set them.
     *
     * @var  array
     */
    public $renew = array(
        'modified',
        'modified_by'
    );

    /**
     * Generate the automatic `modified` value (now).
     *
     * @param   array   $data
     * @return  string
     */
    public function automaticModified($data)
    {
        return \Date::of('now')->toSql();
    }

    /**
     * Generate the automatic `modified_by` value (current user).
     *
     * @param   array   $data
     * @return  integer
     */
    public function automaticModifiedBy($data)
    {
        return (int) \User::get('id');
    }

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
        if (!is_null($status)) {
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
     * Has the mailing reached a terminal (done) state?
     *
     * @return  boolean
     */
    public function isDone()
    {
        return in_array($this->get('status'), array(
            self::STATUS_SENT,
            self::STATUS_CANCELLED
        ));
    }

    /**
     * Is the mailing directly editable in the live editor? Only a draft (or a
     * brand-new mailing) is. Committed-but-not-yet-sending states (held,
     * pending-approval, scheduled) are shown read-only and must be explicitly
     * returned to draft first via canReturnToDraft() / the "Edit draft" action.
     *
     * @return  boolean
     */
    public function isEditable()
    {
        return $this->isNew() || $this->get('status') == self::STATUS_DRAFT;
    }

    /**
     * May the mailing be pulled back to draft so its content can be re-edited?
     * True for launched-but-not-yet-sending states: they hold only a recipient
     * *spec* (the queue is not materialized until sending), so returning to
     * draft is clean. Re-editing then re-runs verify/launch and its gates
     * (approval, hold, schedule) -- it does not bypass them. In-flight
     * (preparing/sending/paused) and terminal (sent/cancelled) mailings cannot.
     *
     * @return  boolean
     */
    public function canReturnToDraft()
    {
        return in_array($this->get('status'), array(
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_HELD,
            self::STATUS_SCHEDULED
        ));
    }

    /**
     * May the mailing be permanently deleted? Only if it never started sending:
     * once delivery has begun (or completed) we keep the record for its state
     * and history and it can only be cancelled, never destroyed. So a mailing is
     * deletable when it has no start timestamp and is in a pre-send state (draft,
     * held, pending-approval, scheduled) or was cancelled before it ever sent.
     *
     * @return  boolean
     */
    public function isDeletable()
    {
        $started = (string) $this->get('started_on');
        if ($started !== '' && $started !== '0000-00-00 00:00:00') {
            return false;
        }

        return in_array($this->get('status'), array(
            self::STATUS_DRAFT,
            self::STATUS_HELD,
            self::STATUS_PENDING_APPROVAL,
            self::STATUS_SCHEDULED,
            self::STATUS_CANCELLED
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
