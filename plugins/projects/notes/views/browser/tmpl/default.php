<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Get helper
$projectsHelper = new ProjectsHelper( $this->database );

$masterscope = 'projects' . DS . $this->project->alias . DS . 'notes';

// Set project (system) group
$group_prefix = $this->config->get('group_prefix', 'pr-');
$group = $group_prefix . $this->project->alias;

// Get notes to choose from
$items = $projectsHelper->getNotes($group, $masterscope);

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
$route = $this->project->provisioned
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
$p_url = JRoute::_($route . a . 'active=notes');

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
				<li class="c-click notes i-missing" id="note::<?php echo $miss['id']; ?>"><?php echo $miss['title']; ?><span class="c-missing"><?php echo JText::_('PLG_PROJECTS_NOTES_MISSING_NOTE'); ?></span></li>
		<?php	}
		}
	}
?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo JText::_('PLG_PROJECTS_NOTES_NO_SELECTION_ITEMS_FOUND'); ?></p>
<?php } ?>

<?php if (!$this->project->provisioned) { ?>
	<p class="addnew">Go to <a href="<?php echo JRoute::_($route).'?active=notes'; ?>">Notes</a> to create a new note</p>
<?php } ?>