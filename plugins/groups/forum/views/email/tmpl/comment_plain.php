<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = rtrim(Request::base(), '/');
$sef  = Route::url($this->thread->link());
$link = $base . '/' . trim($sef, '/');

// Build message
$message = '';
if ($this->delimiter)
{
	$message .= $this->delimiter . "\n";
	$message .= Lang::txt('PLG_GROUPS_FORUM_EMAIL_REPLY_ABOVE') . "\n";
	$message .= 'Message from ' . $base . ' / ' . Lang::txt('PLG_GROUPS_FORUM_DETAILS_THREAD', $this->thread->get('id')) . "\n";
}
$message .= ($this->post->get('anonymous')) ? Lang::txt('PLG_GROUPS_FORUM_ANONYMOUS') : $this->post->creator('name') . ' (' . $this->post->creator('username') . ')';
$message .= ' wrote (in ' . $this->group->get('description') . ': ' . $this->section->get('title') . ' - ' . $this->category->get('title') . ' - ' . $this->thread->get('title') . '):';

$output = html_entity_decode($this->post->content('clean'), ENT_COMPAT, 'UTF-8');
$output = preg_replace_callback(
	"/(&#[0-9]+;)/",
	function($m)
	{
		return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
	},
	$output
);

$message .= $output;
$message .= "\n\n" . Lang::txt('PLG_GROUPS_FORUM_EMAIL_UNSUBSCRIBE') . ":\n" . $this->get('unsubscribe');

$message = preg_replace('/\n{3,}/', "\n\n", $message);

// Output message
echo preg_replace('/<a\s+href="(.*?)"\s?(.*?)>(.*?)<\/a>/i', '\\1', $message) . "\n\n" . $link . "\n";
