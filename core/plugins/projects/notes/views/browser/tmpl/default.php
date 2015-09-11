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

$masterscope = 'projects' . DS . $this->model->get('alias') . DS . 'notes';

// Set project (system) group
$group_prefix = $this->config->get('group_prefix', 'pr-');
$group = $group_prefix . $this->model->get('alias');

// Get our model
$note = new \Components\Projects\Models\Note($masterscope, $group, $this->model->get('id'));

// Get notes to choose from
$items = $note->getNotes();

// Sort notes to display hierarchy by scope
$notes = array();
$order = array();
$thirdlevel = array();

if ($items)
{
	foreach ($items as $note)
	{
		$parts = explode ( '/', $note->scope );
		$remaining = array_slice($parts, 3);
		$level = count($remaining) + 1;
		$parent = $level > 1 ? array_shift($remaining) : '';

		if ($level == 1)
		{
			$notes[$note->pagename] = array( $level => array($note));
		}
		elseif ($level == 2)
		{
			$notes[$parent][$level][] = $note;
		}
		elseif ($level >= 3)
		{
			$r = array_shift($remaining);
			$thirdlevel[$r][] = $note;
		}
	}
}

$missing = array();
$shown = array();

// Attached item
$selected = NULL;
if ($this->primary && !empty($this->attachments))
{
	$selected = $this->attachments[0]->object_name;
}

// Build url
$route = $this->model->isProvisioned()
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->model->get('alias');

?>
<ul id="c-browser" <?php if (count($items) == 0 && isset($this->attachments) && count($this->attachments) == 0) { echo 'class="hidden"'; } ?> >
<?php
	if (count($notes) > 0)
	{ ?>

		<?php foreach ($notes as $note)
		{
			foreach ($note as $level => $parent)
			{
				$p2 = 0;

				foreach ($parent as $entry)
				{ ?>
					<?php if ($level == 1) { ?>
					<li class="c-click notes toplevel" id="note::<?php echo $entry->id; ?>"><?php echo \Hubzero\Utility\String::truncate($entry->title, 35); ?>
					<?php } ?>

					<?php if ($level == 2) {
						$p2++;
						if ($p2 == 1)
						{
							echo '<ol>';
						}
					?>
					<li class="c-click notes wikilevel_2" id="note::<?php echo $entry->id; ?>"><?php echo \Hubzero\Utility\String::truncate($entry->title, 35); ?></li>
					<?php
						if ($p2 == count($parent))
						{
							echo '</ol>';
						}
					 }
					?>

					<?php
						$shown[] = $entry->id;

						// Third level of notes
						if (isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0) {
							foreach ($thirdlevel[$entry->pagename] as $subpage) { ?>
							<li class="c-click notes wikilevel_3" id="note::<?php echo $subpage->id; ?>"><?php echo \Hubzero\Utility\String::truncate($subpage->title, 35); ?></li>

					<?php $shown[] = $subpage->id;	}
					 } ?>

					</li>
		<?php	}
			}
		}

		// Check for missing items
		// Primary content / Supporting docs
		if (isset($this->attachments))
		{
			if (count($this->attachments) > 0)
			{
				foreach ($this->attachments as $attachment)
				{
					if (!in_array($attachment->object_id, $shown))
					{
						// Found missing
						$miss = array();
						$miss['id'] = $attachment->object_id;
						$miss['title'] = $attachment->title;
						$missing[] = $miss;
					}
				}
			}
		}

		// Add missing items
		if (count($missing) > 0)
		{
			foreach ($missing as $miss)
			{ ?>
				<li class="c-click notes i-missing" id="note::<?php echo $miss['id']; ?>"><?php echo $miss['title']; ?><span class="c-missing"><?php echo Lang::txt('PLG_PROJECTS_NOTES_MISSING_NOTE'); ?></span></li>
		<?php	}
		}
	}
?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_NOTES_NO_SELECTION_ITEMS_FOUND'); ?></p>
<?php } ?>

<?php if (!$this->model->isProvisioned()) { ?>
	<p class="addnew">Go to <a href="<?php echo Route::url($route . '&active=notes'); ?>">Notes</a> to create a new note</p>
<?php } ?>