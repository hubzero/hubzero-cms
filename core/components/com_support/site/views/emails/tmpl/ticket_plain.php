<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

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

$message = '';
if (!$this->config->get('email_terse')) {
    $usertype = Lang::txt('COM_SUPPORT_UNKNOWN');
    if ($this->ticket->submitter->get('id')) {
        $gids = array();
        foreach (User::getInstance($this->ticket->submitter->get('id'))->accessgroups() as $g) {
            $gids[] = $g->group_id;
        }
        $usertype = implode(', ', $gids);
    }

    if ($this->delimiter) {
        $message .= $this->delimiter . "\n";
        $message .= Lang::txt('COM_SUPPORT_EMAIL_REPLY_ABOVE') . "\n";
        $ticketId   = $this->ticket->get('id');
        $message .= 'Message from ' . rtrim(Request::base(), '/') . '/support / Ticket #' . $ticketId . "\n";
    }
    $ticketId      = $this->ticket->get('id');
    $ticketSummary = $this->ticket->get('summary');
    $ticketCreated = $this->ticket->get('created');
    $submitterName = $this->ticket->submitter->get('name');
    $ticketLogin   = $this->ticket->get('login');
    $ticketIp      = $this->ticket->get('ip');
    $ticketHost    = $this->ticket->get('hostname');
    $cookiesLabel  = $this->ticket->get('cookies')
        ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED')
        : Lang::txt('COM_SUPPORT_COOKIES_DISABLED');

    $message .= '----------------------------' . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET')) . ': ' . $ticketId . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_SUMMARY')) . ': ' . $ticketSummary . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED')) . ': ' . $ticketCreated . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_CREATED_BY')) . ': '
        . $submitterName . ($ticketLogin ? ' (' . $ticketLogin . ')' : '') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE')) . ': ' . $usertype . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL')) . ': ' . $this->ticket->get('email') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_IP_HOSTNAME')) . ': ' . $ticketIp . ' (' . $ticketHost . ')' . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_OS')) . ': ' . $this->ticket->get('os') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_BROWSER')) . ': ' . $this->ticket->get('browser') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_UAS')) . ': ' . $this->ticket->get('uas') . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_COOKIES')) . ': ' . $cookiesLabel . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_REFERRER')) . ': ' . $this->ticket->get('referrer') . "\n";
    $message .= '----------------------------' . "\n\n";
    $message .= $this->ticket->get('report');
    if ($this->ticket->attachments->count() > 0) {
        $message .= "\n\n";
        foreach ($this->ticket->attachments as $attachment) {
            $message .= $base . '/' . trim(Route::url($attachment->link()), '/') . "\n";
        }
    }
} else {
    $message .= Lang::txt('COM_SUPPORT_NOTIFY_TICKET_CREATED', $this->ticket->get('id'), $link) . "\n";
}

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
