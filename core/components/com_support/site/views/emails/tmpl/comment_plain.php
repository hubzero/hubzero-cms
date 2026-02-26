<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

$base = rtrim(Request::base(), '/');
if (substr($base, -13) == 'administrator') {
    $base = rtrim(substr($base, 0, strlen($base) - 13), '/');
    $sef = 'support/ticket/' . $this->ticket->get('id');
} else {
    $sef = Route::url($this->ticket->link());
}
$link = $base . '/' . trim($sef, '/');

// Build message
$message = '';
if ($this->delimiter) {
    $message .= $this->delimiter . "\n";
    $message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
    $message .= 'Message from ' . $base . '/support / Ticket #' . $this->ticket->get('id') . "\n";
}

$message .= '----------------------------' . "\n";

if (!$this->config->get('email_terse')) {
    $ticketId      = $this->ticket->get('id');
    $ticketSummary = $this->ticket->get('summary');
    $ticketCreated = $this->ticket->get('created');
    $submitterName = $this->ticket->submitter->get('name');
    $ticketLogin   = $this->ticket->get('login');
    $statusTitle   = $this->ticket->status->get('title');
    $ticketTags    = $this->ticket->tags('string');

    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')) . ': ' . $ticketId . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')) . ': ' . $ticketSummary . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')) . ': ' . $ticketCreated . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')) . ': '
        . $submitterName . ($ticketLogin ? ' (' . $ticketLogin . ')' : '') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_STATUS')) . ': ' . $statusTitle . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_TAGS')) . ': ' . $ticketTags . "\n";
    $message .= '----------------------------' . "\n\n";
    if ($this->comment->isPrivate()) {
        $message .= '!! ' . Lang::txt('COM_SUPPORT_COMMENT_PRIVATE') . " !!\n";
    }
    $commentPosted  = Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_POSTED', $ticketId);
    $creatorName    = $this->comment->creator->get('name');
    $creatorLogin   = $this->comment->creator->get('username');
    $commentCreated = $this->comment->get('created');
    $message .= $commentPosted . ': ' . $creatorName . '(' . $creatorLogin . ")\n";
    $message .= Lang::txt('COM_SUPPORT_TICKET_EMAIL_COMMENT_CREATED') . ': ' . $commentCreated . "\n\n";
    if ($this->comment->changelog()->lists()) {
        foreach ($this->comment->changelog()->lists() as $type => $log) {
            if (is_array($log) && count($log) > 0) {
                foreach ($log as $items) {
                    if ($type == 'changes') {
                        $message .= ' * ' . Lang::txt(
                            'COM_SUPPORT_CHANGELOG_BEFORE_AFTER',
                            $items->field,
                            $items->before,
                            $items->after
                        ) . "\n";
                    } elseif ($type == 'notifications') {
                        $message  .= ' * ' . Lang::txt(
                            'COM_SUPPORT_CHANGELOG_NOTIFIED',
                            $items->role,
                            $items->name,
                            $items->address
                        ) . "\n";
                    }
                }
                $message .= "\n";
            }
        }
    }
    $message .= preg_replace("/<br\s?\/>/i", '', $this->comment->get('comment'));
    if ($this->comment->attachments->count() > 0) {
        $message .= "\n\n";
        foreach ($this->comment->attachments as $attachment) {
            $message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
        }
    }
} else {
    $message .= Lang::txt('COM_SUPPORT_NOTIFY_TICKET_UPDATED', $this->ticket->get('id'), $link) . "\n";
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
