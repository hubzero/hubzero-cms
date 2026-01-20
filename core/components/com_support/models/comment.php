<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Validate;
use Route;
use User;
use Lang;

require_once __DIR__ . DS . 'attachment.php';
require_once __DIR__ . DS . 'changelog.php';

/**
 * Support ticket comment model
 */
class Comment extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    public $namespace = 'support';

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
    public $orderDir = 'asc';

    /**
     * Fields and their validation criteria
     *
     * @var  array
     */
    protected $rules = array(
        //'comment' => 'notempty',
        'ticket'  => 'positive|nonzero'
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
     * Base URL
     *
     * @var string
     */
    private $base = null;

    /**
     * Changelog
     *
     * @var  object
     */
    private $changeLog = null;

    /**
     * Cached data
     *
     * @var array
     */
    private $cache = array(
        'recipients.added'  => array(),
        'recipients.failed' => array()
    );

    /**
     * Is the comment private?
     *
     * @return  boolean
     */
    public function isPrivate()
    {
        return ($this->get('access') == 1);
    }

    /**
     * Get parent ticket
     *
     * @return  object
     */
    public function ticket()
    {
        return $this->belongsToOne(__NAMESPACE__ . '\\Ticket', 'ticket');
    }

    /**
     * Defines a belongs to one relationship between comment and user
     *
     * @return  object
     */
    public function creator()
    {
        return $this->belongsToOne('Hubzero\User\User', 'created_by');
    }

    /**
     * Get a list of attachments
     *
     * @return  object
     */
    public function attachments()
    {
        return $this->oneToMany(__NAMESPACE__ . '\\Attachment', 'comment_id');
    }

    /**
     * Return a formatted timestamp
     *
     * @param   string  $as  What format to return
     * @return  boolean
     */
    public function created($as = '')
    {
        $as = strtolower($as);
        $dt = $this->get('created');

        if ($as == 'date') {
            $dt = Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
        }

        if ($as == 'time') {
            $dt = Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
        }

        return $dt;
    }

    /**
     * Save the record
     *
     * @return  boolean  False if error, True on success
     */
    public function save()
    {
        $this->set('changelog', $this->changelog()->toString());

        return parent::save();
    }

    /**
     * Delete the record and all associated data
     *
     * @return  boolean  False if error, True on success
     */
    public function destroy()
    {
        // Remove data
        foreach ($this->attachments()->rows() as $attachment) {
            if (!$attachment->destroy()) {
                $this->addError($attachment->getError());
                return false;
            }
        }

        // Attempt to delete the record
        return parent::destroy();
    }

    /**
     * Get parsed comment
     *
     * @return  object
     */
    public function transformComment()
    {
        if (is_null($this->get('comment_transformed'))) {
            $comment  = (string)$this->get('comment');
            $original = $comment;

            // Replace {ticket#123}
            $ticketUrl = Route::url("index.php?option=com_support&task=ticket&id=$1");
            $ticketLink = '<a href="' . $ticketUrl . '">' . Lang::txt('ticket #%s', "$1") . '</a>';
            $comment = preg_replace('/\{ticket#([\d]+)\}/i', $ticketLink, $comment);

            // Handle legacy attachment strings
            $comment = preg_replace_callback('/\{attachment#[0-9]*\}/sU', array(&$this,'_getAttachment'), $comment);

            $this->set('comment', $comment);

            // Prepare comment
            $results = \Event::trigger('support.onCommentPrepare', array('com_support.comment', &$this));
            $results = implode('', $results);

            $comment = $this->get('comment');

            if (!trim($results)) {
                $comment = preg_replace("/<br\s?\/>/i", '', $comment);
                $comment = htmlentities($comment, ENT_COMPAT, 'UTF-8');
                $comment = nl2br($comment);
                $comment = str_replace("\t", ' &nbsp; &nbsp;', $comment);
                $comment = preg_replace('/  /', ' &nbsp;', $comment);
            }

            $this->set('comment_transformed', $comment);
            $this->set('comment', $original);
        }

        return $this->get('comment_transformed');
    }

    /**
     * Process an attachment macro and output a link to the file
     *
     * @param   array   $matches  Macro info
     * @return  string
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _getAttachment($matches)
    {
        $tokens = explode('#', $matches[0]);

        $id = intval(end($tokens));

        $attach = Attachment::oneOrNew($id);

        if ($attach->get('id') && !$attach->get('comment_id')) {
            $attach->set('comment_id', $this->get('id'));
            $attach->set('created', $this->get('created'));
            $attach->set('created_by', $this->get('created_by'));
            $attach->save();
        }

        return '';
    }

    /**
     * Generate and return various links to the entry
     * Link will vary depending upon action desired, such as edit, delete, etc.
     *
     * @param   string  $type  The type of link to return
     * @return  string
     */
    public function link($type = '')
    {
        if (!isset($this->base)) {
            $this->base = 'index.php?option=com_support&task=ticket&id=' . $this->get('ticket');
        }
        $link = $this->base;

        // If it doesn't exist or isn't published
        switch (strtolower($type)) {
            case 'component':
            case 'base':
                return $this->base;
            break;

            case 'permalink':
            default:
                $link .= '#c' . $this->get('id');
                break;
        }

        return $link;
    }

    /**
     * Get the changelog
     *
     * @return  object
     */
    public function changelog()
    {
        if (!($this->changeLog instanceof Changelog)) {
            $this->changeLog = new Changelog($this->get('changelog'));
        }
        return $this->changeLog;
    }

    /**
     * Add to the recipient list
     *
     * @param   string  $to
     * @param   string  $role
     * @return  object
     */
    public function addTo($to, $role = '')
    {
        $added = false;

        // User ID
        if (is_numeric($to)) {
            $user = User::getInstance($to);

            if (is_object($user) && $user->get('id')) {
                if (isset($this->cache['recipients.added'][$user->get('email')])) {
                    return $this;
                }
                $this->cache['recipients.added'][$user->get('email')] = array(
                    'role'    => $role,
                    'name'    => $user->get('name'),
                    'email'   => $user->get('email'),
                    'id'      => $user->get('id')
                );
                $added = true;
            }
        } elseif (is_string($to)) {
            // Email
            if (strstr($to, '@') && Validate::email($to)) {
                if (isset($this->cache['recipients.added'][$to])) {
                    return $this;
                }
                $this->cache['recipients.added'][$to] = array(
                    'role'    => $role,
                    'name'    => Lang::txt('COM_SUPPORT_UNKNOWN'),
                    'email'   => $to,
                    'id'      => 0
                );
                $added = true;
            } else {
                // Username
                $user = User::getInstance($to);
                if (is_object($user) && $user->get('id')) {
                    if (isset($this->cache['recipients.added'][$user->get('email')])) {
                        return $this;
                    }
                    $this->cache['recipients.added'][$user->get('email')] = array(
                        'role'    => $role,
                        'name'    => $user->get('name'),
                        'email'   => $user->get('email'),
                        'id'      => $user->get('id')
                    );
                    $added = true;
                }
            }
        } elseif (is_array($to)) {
            if (isset($this->cache['recipients.added'][$to['email']])) {
                return $this;
            }
            $this->cache['recipients.added'][$to['email']] = $to;
            $added = true;
        }

        if (!$added) {
            $this->cache['recipients.failed'][] = $to;
        }

        return $this;
    }

    /**
     * Get the recipient list
     *
     * @param   string  $to
     * @return  array
     */
    public function to($who = '')
    {
        $who = strtolower(trim($who));

        switch ($who) {
            case 'id':
            case 'ids':
                $tos = array();
                foreach ($this->cache['recipients.added'] as $to) {
                    if ($to['id']) {
                        $tos[] = $to;
                    }
                }
                return $tos;
            break;

            case 'email':
            case 'emails':
                $tos = array();
                foreach ($this->cache['recipients.added'] as $to) {
                    if (!$to['id'] && $to['email']) {
                        $tos[] = $to;
                    }
                }
                return $tos;
            break;
        }

        return $this->cache['recipients.added'];
    }
}
