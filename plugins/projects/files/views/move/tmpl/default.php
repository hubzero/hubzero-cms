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
$f = 1;
$i = 1;
$skipped = 0;
$maxlevel = 100;

// Get remote connection
$objRFile = new ProjectRemoteFile ($this->database);

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

// Get all parents
$dirs = array();
foreach ($this->list as $item)
{
	if ($item->type == 'folder')
	{
		$dirs[] = $item->localPath;
	}
}

?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_MOVE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="<?php echo $this->do; ?>" value="moveit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p><?php echo JText::_('COM_PROJECTS_MOVE_FILES_CONFIRM'); ?></p>

		<ul class="sample">
		<?php foreach ($this->items as $element)
		{
			$remote = NULL;
			$skip 	= false;
			foreach ($element as $type => $item)
			{
				// Get type and item name
			}

			// Remote file?
			if (!empty($this->services))
			{
				foreach ($this->services as $servicename)
				{
					// Get stored remote connection to file
					$fpath  = $this->subdir ? $this->subdir . DS . $item : $item;
					$remote = $objRFile->getConnection($this->project->id, '', $servicename, $fpath);
					if ($remote)
					{
						break;
					}
				}
			}

			// Display list item with file data
			$this->view('default', 'selected')
			     ->set('skip', $skip)
			     ->set('item', $item)
			     ->set('remote', $remote)
			     ->set('type', $type)
			     ->set('action', 'delete')
			     ->set('multi', 'multi')
			     ->display();
		} ?>
		</ul>

		<div id="dirs" class="dirs">
			<h4><?php echo JText::_('COM_PROJECTS_MOVE_WHERE'); ?></h4>
			<?php if (count($dirs) > 0) {  echo '<ul class="dirtree">';
			?>
				<li>
					<input type="radio" name="newpath" value="" <?php if(!$this->subdir) { echo 'disabled="disabled" '; } ?> checked="checked" /> <span><?php echo JText::_('COM_PROJECTS_HOME_DIRECTORY'); ?></span>
				</li>
			<?php
			for ($i= 0; $i < count($dirs); $i++) {
					$dir = $dirs[$i];
					// Remove full path
					$dir 			= trim(str_replace($this->path, "", $dir), DS);
					$desect_path 	= explode(DS, $dir);
					$level 			= count($desect_path);
					$dirname 		= end($desect_path);
					$maxlevel 		= $level > $maxlevel ? $level : $maxlevel;

					$leftMargin = ($level * 15) . 'px';
				 ?>
				<li style="margin-left:<?php echo $leftMargin; ?>">
					<input type="radio" name="newpath" value="<?php echo urlencode($dir); ?>" <?php if($this->subdir == $dir) { echo 'disabled="disabled" '; } ?> /> <span><span class="folder <?php if($this->subdir == $dir) { echo 'prominent '; } ?>"><?php echo $dirname; ?></span></span>
				</li>
			<?php }
			echo '</ul>'; }
			if ($maxlevel <= 100) { ?>
			<?php if (count($dirs) > 0) { ?>
				<div class="or"><?php echo JText::_('COM_PROJECTS_OR'); ?></div>
			<?php }  ?>
			<label><span class="block"><?php echo JText::_('COM_PROJECTS_MOVE_TO_NEW_DIRECTORY'); ?></span>
				<span class="mini prominent"><?php echo $this->subdir ? $this->subdir.DS : ''; ?></span>
				<input type="text" name="newdir" maxlength="50" value="" />
			</label>
			<?php }  ?>
		</div>
		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo JText::_('COM_PROJECTS_MOVE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
				<span>
					<a id="cancel-action"  class="btn btn-cancel"  href="<?php echo $this->url . '?a=1' .$subdirlink; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
				</span>
			<?php } ?>
		</p>
	</fieldset>
</form>
<?php } ?>
</div>