<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
