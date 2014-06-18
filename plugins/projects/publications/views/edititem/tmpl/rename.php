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

$title 		 = JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_RELABEL');
$placeholder = JText::_('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');

if ($this->row->type == 'file')
{
	$dirpath = dirname($this->row->path) == '.' ? '' : dirname($this->row->path) . DS;
	$gone 	 = is_file($this->path . DS . $this->row->path) ? false : true;
	$title   = $gone ? JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_RELABEL') : JText::_('PLG_PROJECTS_PUBLICATIONS_CONTENT_RENAME');
	$placeholder = basename($this->row->path);
}

?>
<div id="abox-content">
<h3><?php echo $title; ?></h3>
	<form id="hubForm-ajax" method="post" action="<?php  ?>">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="aid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="props" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="action" value="saveitem" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="backUrl" value="<?php echo $this->backUrl; ?>" />
				<?php if ($this->project->provisioned == 1 ) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="content-wrap">
				<div class="content-edit">
					<label for="title">
						<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_LABEL')); ?>:</span>
						<input type="text" name="title" maxlength="250" class="long" value="<?php echo $this->row && $this->row->title ? $this->row->title : ''; ?>" placeholder="<?php echo $placeholder; ?>"  />
						<span class="optional"><?php echo JText::_('OPTIONAL'); ?></span>
					</label>

					<?php if ($this->row->type == 'file') { ?>
						<?php if ($gone) { ?>
						<p>
							<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_FILE_PATH')); ?>*:</span>
							<span><?php echo $this->row->path; ?></span>
						</p>
						<p class="content-hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_FILE_MISSING_NOTICE'); ?></p>
						<?php } else { ?>
						<p>
							<span class="leftshift faded"><?php echo ucfirst(JText::_('PLG_PROJECTS_PUBLICATIONS_FILE_PATH')); ?>*:</span>
							<span><?php echo $dirpath; ?> <input type="text" name="filename" maxlength="100" value="<?php echo basename($this->row->path); ?>" /></span>
						</p>
						<p class="content-hint"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_RENAME_NOTICE'); ?></p>
						<?php } ?>
					<?php } ?>

					<p class="submitarea">
						<input type="submit" class="btn" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>" />
						<?php if ($this->ajax) { ?>
						<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?>" />
						<?php } else { ?>
						<a href="<?php echo $this->backUrl; ?>" class="btn btn-cancel"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?></a>
						<?php } ?>
					</p>
				</div>
			</div>
	</form>
	<div class="clear"></div>
</div>