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

// Sort notes to display hierarchy by scope
$notes = array();
$order = array();
$thirdlevel = array();

$pNotes = $this->model->getNotes();
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

		$parts = explode ( '/', $note->scope );
		$remaining = array_slice($parts, 3);
		$level = count($remaining) + 1;
		$parent = $level > 1 ? array_shift($remaining) : '';

		if ($level == 1) {
			$notes[$note->pagename] = array( $level => array($note));
		}
		else if ($level == 2) {
			$notes[$parent][$level][] = $note;
		}
		else if ($level >= 3) {
			$r = array_shift($remaining);
			$thirdlevel[$r][] = $note;
		}
	}
}
?>
<div class="notes-list">
	<h4><?php echo ucfirst(JText::_('COM_PROJECTS_NOTES_MULTI')); ?></h4>
	<ul>
	<?php if ($notes) { ?>
		<?php foreach ($notes as $note) {
			    foreach ($note as $level => $parent) {
				 foreach ($parent as $entry) { ?>
					<li <?php if ($entry->pagename == $this->page->get('pagename')) { echo 'class="active"'; } ?>>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$entry->scope.a.'pagename='.$entry->pagename); ?>" class="note wikilevel_<?php echo $level; ?>"><?php echo \Hubzero\Utility\String::truncate($entry->title, 35); ?></a>
					</li>
					<?php
						// Third level of notes
						if (isset($thirdlevel[$entry->pagename]) && count($thirdlevel[$entry->pagename]) > 0) {
							foreach ($thirdlevel[$entry->pagename] as $subpage) { ?>
							<li <?php if ($subpage->pagename == $this->page->get('pagename')) { echo 'class="active"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes'.a.'scope='.$subpage->scope.a.'pagename='.$subpage->pagename); ?>" class="note wikilevel_3"><?php echo \Hubzero\Utility\String::truncate($subpage->title, 35); ?></a>
							</li>
					<?php	}
					 } ?>
		<?php	}
			}
		?>
	<?php } ?>
	<?php } else { ?>
	<li class="faded"><?php echo JText::_('COM_PROJECTS_NOTES_NO_NOTES'); ?></li>
	<?php } ?>
	</ul>
</div>

