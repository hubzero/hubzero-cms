<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

// Sort notes to display hierarchy by scope
$notes = array();
$order = array();
$thirdlevel = array();

$pNotes = $this->note->getNotes();
if ($pNotes)
{
	foreach ($pNotes as $note)
	{
		$show = 1;

		if (!$show)
		{
			// Skip
			continue;
		}

		$level = 1;
		$parent = '';

		if (trim($note->path))
		{
			$parts = explode('/', trim($note->path));
			$level = count($parts) + 1;
			$parent = $level > 1 ? array_shift($parts) : '';
		}

		if ($level == 1)
		{
			$notes[$note->pagename] = array($level => array($note));
		}
		else if ($level == 2)
		{
			$notes[$parent][$level][] = $note;
		}
		else if ($level >= 3)
		{
			$thirdlevel[$parent][] = $note;
		}
	}
}
?>
<div class="notes-list">
	<h4><?php echo ucfirst(Lang::txt('COM_PROJECTS_NOTES_MULTI')); ?></h4>
	<ul>
		<?php if ($notes) { ?>
			<?php
			foreach ($notes as $note)
			{
				foreach ($note as $level => $parent)
				{
					foreach ($parent as $entry)
					{
						?>
						<li <?php if ($entry->pagename == $this->page->get('pagename')) { echo 'class="active"'; } ?>>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&pagename=' . $entry->pagename); ?>" class="note wikilevel_<?php echo $level; ?>"><?php echo \Hubzero\Utility\String::truncate($entry->title, 35); ?></a>
						</li>
						<?php
						// Third level of notes
						if (isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0)
						{
							foreach ($thirdlevel[$entry->pagename] as $subpage)
							{
								?>
								<li <?php if ($subpage->pagename == $this->page->get('pagename')) { echo 'class="active"'; } ?>>
									<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->project->get('alias') . '&active=notes&pagename=' . $subpage->pagename); ?>" class="note wikilevel_3"><?php echo \Hubzero\Utility\String::truncate($subpage->title, 35); ?></a>
								</li>
								<?php
							}
						}
					}
				}
			}
			?>
		<?php } else { ?>
			<li class="faded"><?php echo Lang::txt('COM_PROJECTS_NOTES_NO_NOTES'); ?></li>
		<?php } ?>
	</ul>
</div>

