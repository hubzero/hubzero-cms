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
$v = count($this->versions);
?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_FILES_SHOW_HISTORY'); ?></h3>
<?php
// Display error
if ($this->getError()) { 
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) { 
?>
<form id="hubForm-ajax" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="diff" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<ul>
			<?php 
				// Get file extention
				$path = $this->subdir ? $this->path.DS.$this->subdir : $this->path;
				$ext = ProjectsHtml::getFileAttribs( $this->file, $path, 'ext' );
			?>
			<li>	<img src="<?php echo ProjectsHtml::getFileIcon($ext); ?>" alt="<?php echo urldecode($this->file); ?>" />
			<?php
				echo urldecode($this->file); 
				echo '<input type="hidden" name="asset[]" value="'.$this->file.'" /></li>'; 
			?>
			</ul>
			<table class="revisions">
				<thead>
					<tr>
						<td><?php echo ucfirst(JText::_('COM_PROJECTS_REVISION')); ?></td>
						<td><?php echo ucfirst(JText::_('COM_PROJECTS_CREATED')); ?></td>
						<td><?php echo ucfirst(JText::_('COM_PROJECTS_BY')); ?></td>
						<td></td>
					</tr>
				</thead>
				<tbody>
			<?php foreach($this->versions as $version) { ?>
				<tr>
					<td><?php echo $v; ?></td>
					<td><?php echo $version['date']; ?></td>
					<td><?php echo $version['author']; ?></td>
					<td><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'active=files'.a.'id='.$this->project->id).'/?subdir='.urlencode($this->subdir).a.'asset[]='.urlencode($this->file).a.'case='.$this->case.a.'action=download'.a.'hash='.$version['hash']; ?>" class="download_file" title="<?php echo JText::_('COM_PROJECTS_DOWNLOAD'); ?>" >&nbsp;</a>	</td>
				</tr>
			<?php if($version['content']) { ?>
				<tr class="commitmsg">
					<td></td>
					<td colspan="3"><?php echo $version['content']; ?></td>
				</tr>	
			<?php } ?>
			<?php if($version['status']) { ?>
				<tr class="commitstatus">
					<td></td>
					<td colspan="3"><?php echo $version['status']; ?></td>
				</tr>
			<?php } ?>
			<?php $v--; } ?>
				</tbody>
			</table>
		</fieldset>
</form>
<?php } ?>
</div>