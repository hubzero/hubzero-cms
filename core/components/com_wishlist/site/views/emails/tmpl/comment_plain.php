<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

// Build link to wish
$base = rtrim(Request::base(), '/');
$base = rtrim(str_replace('/administrator', '', $base), '/');
$link = $base . '/' . ltrim(Route::url($this->wish->link()), '/');

// Get author name
$name  = $this->wish->proposer('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
$login = $this->wish->proposer('username', Lang::txt('COM_WISHLIST_UNKNOWN'));

if ($this->wish->get('anonymous'))
{
	$name  = Lang::txt('COM_WISHLIST_ANONYMOUS');
	$login = Lang::txt('COM_WISHLIST_ANONYMOUS');
}

// Build message
$message  = '----------------------------' . "\n";
$message .= Lang::txt('COM_WISHLIST_WISH') . ' #' . $this->wish->get('id') . ', ' . $this->wishlist->get('title') . ' ' . Lang::txt('COM_WISHLIST_WISHLIST') . "\n";
$message .= Lang::txt('COM_WISHLIST_WISH_DETAILS_SUMMARY') . ': ' . stripslashes($this->wish->get('subject')) . "\n";
$message .= Lang::txt('COM_WISHLIST_PROPOSED_ON') . ' ' . $this->wish->proposed() . "\n";
$message .= Lang::txt('COM_WISHLIST_BY') . ' ' . $name . ' ' . ($this->wish->get('anonymous') ? '' : '(' . $login . ' )') . "\n";
$message .= '----------------------------' . "\n\n";

// Get author name
$name  = $this->comment->creator('name', Lang::txt('COM_WISHLIST_UNKNOWN'));
$login = $this->comment->creator('username', Lang::txt('COM_WISHLIST_UNKNOWN'));

if ($this->comment->get('anonymous'))
{
	$name  = Lang::txt('COM_WISHLIST_ANONYMOUS');
	$login = Lang::txt('COM_WISHLIST_ANONYMOUS');
}

$message .= Lang::txt('COM_WISHLIST_MSG_COMMENT_BY') . ' ' . $name . ' ';
$message .= $this->comment->get('anonymous') ? '' : '(' . $login . ')';
$message .= ' ' . Lang::txt('COM_WISHLIST_MSG_POSTED_ON').' '. $this->comment->created() . ':' . "\r\n";
$message .= $this->comment->content('clean') . "\r\n";
$message .= $this->comment->get('attachment');

$message .= "\n\n";
$message .= Lang::txt('COM_WISHLIST_GO_TO') . ' ' . $link . ' ' . Lang::txt('COM_WISHLIST_TO_VIEW_THIS_WISH') . '.';

$message = preg_replace('/\n{3,}/', "\n\n", $message);

echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message);
