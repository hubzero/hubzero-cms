<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$parts = explode('/', $note->scope);
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
$selected = null;
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
	{
		foreach ($notes as $note)
		{
			foreach ($note as $level => $parent)
			{
				$p2 = 0;

				foreach ($parent as $entry)
				{
					if ($level == 1)
					{
						?>
						<li class="c-click notes toplevel" id="note::<?php echo $entry->id; ?>"><?php echo \Hubzero\Utility\Str::truncate($entry->title, 35); ?>
						<?php
					}

					if ($level == 2)
					{
						$p2++;
						if ($p2 == 1)
						{
							echo '<ol>';
						}
						?>
						<li class="c-click notes wikilevel_2" id="note::<?php echo $entry->id; ?>"><?php echo \Hubzero\Utility\Str::truncate($entry->title, 35); ?></li>
						<?php
						if ($p2 == count($parent))
						{
							echo '</ol>';
						}
					}

					$shown[] = $entry->id;

					// Third level of notes
					if (isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0)
					{
						foreach ($thirdlevel[$entry->pagename] as $subpage)
						{
							?>
							<li class="c-click notes wikilevel_3" id="note::<?php echo $subpage->id; ?>"><?php echo \Hubzero\Utility\Str::truncate($subpage->title, 35); ?></li>
							<?php
							$shown[] = $subpage->id;
						}
					}
					?>
					</li>
					<?php
				}
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
			{
				?>
				<li class="c-click notes i-missing" id="note::<?php echo $miss['id']; ?>">
					<?php echo $miss['title']; ?>
					<span class="c-missing"><?php echo Lang::txt('PLG_PROJECTS_NOTES_MISSING_NOTE'); ?></span>
				</li>
				<?php
			}
		}
	}
	?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_NOTES_NO_SELECTION_ITEMS_FOUND'); ?></p>
<?php } ?>

<?php if (!$this->model->isProvisioned()) { ?>
	<p class="addnew">Go to <a href="<?php echo Route::url($route . '&active=notes'); ?>">Notes</a> to create a new note</p>
<?php }
