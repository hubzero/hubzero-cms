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
$default_title = ($this->type == 'file') ? basename($this->item) : $this->item;

$name    = ProjectsHtml::shortenFileName(basename($this->item), 70);
$dirname = dirname($this->item);
$inDir	 = $dirname && $dirname != '.' ? ' in /' . ProjectsHtml::shortenFileName(basename($dirname), 40) : '';

?>
<div id="abox-content">
<h3><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_EDIT_ITEM'); ?></h3>
<?php
// Display error  message
if ($this->getError()) {
	echo ('<p class="error">'.$this->getError().'</p>');
} else { ?>

	<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
			<fieldset >
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="action" value="saveitem" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->vid; ?>" />
				<input type="hidden" name="item" value="<?php echo $this->item; ?>" />
				<input type="hidden" name="role" value="<?php echo $this->role; ?>" />
				<input type="hidden" name="type" value="<?php echo $this->type; ?>" />
				<input type="hidden" name="move" value="<?php echo $this->move; ?>" />
				<input type="hidden" name="selections" id="ajax-selections" value="" />
				<input type="hidden" name="provisioned" id="provisioned" value="<?php echo $this->project->provisioned == 1 ? 1 : 0; ?>" />
				<?php if($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="content-edit">
				<p><span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_ITEM')); ?>:</span>
					<?php echo '<span class="prominent">' . $name. '</span>' . $inDir;  ?>
				</p>
				<label for="title">
					<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_DESCRIPTION')); ?>:</span>
					<input type="text" name="title" maxlength="100" class="long" value="<?php echo $this->row && $this->row->title ? $this->row->title : ''; ?>"  />
					<span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
				</label>
				<p class="submitarea">
					<input type="submit" class="btn" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>" />
					<?php if($this->ajax) { ?>
					<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
					<?php } else {
						$rtn = JRequest::getVar('HTTP_REFERER', $this->url, 'server');
					?>
					<a href="<?php echo $rtn; ?>" class="btn btn-cancel"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
					<?php } ?>
				</p>
			</div>
	</form>
	<div class="clear"></div>
<?php } ?>
</div>