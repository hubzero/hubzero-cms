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
    $base = substr($base, 0, strlen($base) - 13);
    $sef = 'support/ticket/new?case=' . $this->report->id;
} else {
    $sef = Route::url(
        'index.php?option=' . $this->option
        . '&controller=' . $this->controller
        . '&task=new&case=' . $this->report->id
    );
}
$link = rtrim($base, '/') . '/' . trim($sef, '/');

$base = rtrim(str_replace('/administrator', '', $base), '/');

$message  = '----------------------------' . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_CASE_NUM')) . ': ' . $this->report->id . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REASON')) . ': ' . $this->report->subject . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REPORTED')) . ': ' . $this->report->created . "\n";
if (!$this->author) {
    $reporter     = User::getInstance($this->report->created_by);
    $reporterName = $this->escape($reporter->get('name'));
    $reporterUser = $this->escape($reporter->get('username'));
    $reporterCmts = $this->escape($this->report->report);

    $message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REPORTED_BY')) . ': '
        . $reporterName . '(' . $reporterUser . ')' . "\n";
    $message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_COMMENTS')) . ': "' . $reporterCmts . '"' . "\n";
}
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_REVIEWED')) . ': ' . $this->report->reviewed . "\n";
$message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_STATUS')) . ': '
    . Lang::txt('COM_SUPPORT_EMAIL_STATUS_REMOVED') . "\n";
if ($this->report->note && !$this->author) {
    $message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_NOTE')) . ': ' . $this->report->note . "\n";
}
if ($this->author) {
    $disputeText = strip_tags(Lang::txt('COM_SUPPORT_EMAIL_DISPUTE_EXPLANATION', '#', $this->report->id));
    $message .= strtoupper(Lang::txt('COM_SUPPORT_EMAIL_DISPUTE')) . ': ' . $disputeText . "\n";
}
$message .= '----------------------------' . "\n\n";

if ($this->reported) {
    $this->commentor = User::getInstance($this->reported->author);
    $commentorName   = stripslashes($this->commentor->get('name'));
    $commentorUser   = $this->commentor->get('username');

    $message .= Lang::txt('COM_SUPPORT_EMAIL_CREATED_BY') . ': '
        . $commentorName . ' (' . $commentorUser . ')' . "\n";
    $message .= Lang::txt('COM_SUPPORT_EMAIL_CREATED') . ': ' . $this->reported->created . "\n\n";

    $message .= str_replace('<br />', '', $this->reported->text);
} else {
    $message .= Lang::txt('COM_SUPPORT_EMAIL_REPORTED_ITEM_NOT_FOUND') . "\n\n";
}
$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n";
