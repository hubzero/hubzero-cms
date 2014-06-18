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

// Get remote connection
$objRFile = new ProjectRemoteFile ($this->database);

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_DELETE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" class="" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="<?php echo $this->do ?>" value="removeit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="case" value="<?php echo $this->case; ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />

		<p><?php echo JText::_('COM_PROJECTS_DELETE_FILES_CONFIRM'); ?></p>

		<ul class="sample">
		<?php foreach ($this->items as $element)
		{
			$skip 	= false;
			$remote = NULL;

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

		<?php if (!empty($this->services) && $skipped > 0)  { ?>
			<p class="notice"><?php echo JText::_('COM_PROJECTS_FILES_DELETE_REMOTE_NEED_CONNECTION'); ?></p>
		<?php } ?>

		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo JText::_('COM_PROJECTS_DELETE'); ?>" id="submit-ajaxform" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
					<a id="cancel-action" href="<?php echo $this->url . '?a=1' .$subdirlink; ?>" class="btn btn-cancel"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
			<?php } ?>
		</p>
	</fieldset>
</form>
<?php } ?>
</div>