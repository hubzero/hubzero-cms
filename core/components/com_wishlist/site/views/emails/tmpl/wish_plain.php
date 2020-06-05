<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Build link to wish
$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');
$link = $base . '/' . ltrim(Route::url($this->wish->link()), '/');

// Get author name
$name  = $this->wish->proposer->get('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
$login = $this->wish->proposer->get('username', Lang::txt('COM_WISHLIST_UNKNOWN'));

if ($this->wish->get('anonymous'))
{
	$name  = Lang::txt('JANONYMOUS');
	$login = Lang::txt('JANONYMOUS');
}

// Build message
$message  = '----------------------------' . "\n";
$message .= Lang::txt('COM_WISHLIST_WISH') . ' #' . $this->wish->get('id') . ', ' . $this->wishlist->get('title') . ' ' . Lang::txt('COM_WISHLIST_WISHLIST') . "\n";
$message .= Lang::txt('COM_WISHLIST_WISH_DETAILS_SUMMARY') . ': ' . stripslashes($this->wish->get('subject')) . "\n";
$message .= Lang::txt('COM_WISHLIST_PROPOSED_ON') . ' ' . $this->wish->proposed() . "\n";
$message .= Lang::txt('COM_WISHLIST_BY') . ' ' . $name . ' ' . ($this->wish->get('anonymous') ? '' : '(' . $login . ' )') . "\n";
$message .= '----------------------------' . "\n\n";

switch ($this->action)
{
	case 'assigned':
	case 'created':
		$content = html_entity_decode(strip_tags($this->wish->get('about')), ENT_COMPAT, 'UTF-8');
		$message .= html_entity_decode($content, ENT_QUOTES, 'UTF-8');
	break;

	case 'moved':
		$message .= Lang::txt('COM_WISHLIST_WISH_TRANSFERRED_FROM_WISHLIST') . ' "' . $this->oldlist->get('title') . '" to "' . $this->wishlist->get('title') . '"';
	break;

	case 'updated':
		if ($this->status != 'pending')
		{
			$message .= Lang::txt('COM_WISHLIST_YOUR_WISH') . ' ' . Lang::txt('COM_WISHLIST_HAS_BEEN') . ' ' . $this->status . ' ' . Lang::txt('COM_WISHLIST_BY_LIST_ADMINS').'.'."\n";
		}
		else
		{
			$message .= Lang::txt('COM_WISHLIST_MSG_WISH_STATUS_CHANGED_TO') . ' ' . $this->status . ' ' . Lang::txt('COM_WISHLIST_BY_LIST_ADMINS').'.'."\n";
		}
	break;
}
$message .= "\n\n";
$message .= Lang::txt('COM_WISHLIST_GO_TO') . ' ' . $link . ' ' . Lang::txt('COM_WISHLIST_TO_VIEW_THIS_WISH').'.';

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message);
