<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined('_HZEXEC_') or die();

if (!$this->comments || count($this->comments) == 0)
{
	return false;
}
// Show Comments
?>
<ol class="comments" id="comments_<?php echo $this->activity->id; ?>">
	<?php foreach ($this->comments as $comment)
	{
		// Show comments
		$this->view('_comment')
	     ->set('comment', $comment)
	     ->set('model', $this->model)
		 ->set('activity', $this->activity)
		 ->set('uid', $this->uid)
		 ->set('edit', $this->edit)
	     ->display();
	} ?>
</ol>

