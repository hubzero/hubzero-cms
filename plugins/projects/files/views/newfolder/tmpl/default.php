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

$subdirlink = $this->subdir ? a . 'subdir=' . urlencode($this->subdir) : '';

?>

<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_FILES_ADD_NEW_FOLDER'); ?> <?php if ($this->subdir) { ?> <?php echo JText::_('COM_PROJECTS_IN'); ?> <span class="folder"><?php echo $this->subdir; ?></span> <?php } ?></h3>
<?php
// Display error
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
	<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
		<fieldset>
			<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
			<input type="hidden" name="<?php echo $this->do ?>" value="savedir" />
			<label>
				<span class="block">&nbsp;</span>
				<img src="/plugins/projects/files/images/folder.gif" alt="" />
				<input type="text" name="newdir" maxlength="100" value="untitled" />
			</label>
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE'); ?>" />
			<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</fieldset>
	</form>
</div>