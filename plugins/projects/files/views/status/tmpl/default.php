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
<h3><?php echo JText::_('COM_PROJECTS_FILES_GIT_STATUS'); ?></h3>
<form id="hubForm-ajax" method="post" action="<?php echo JRoute::_('index.php?option='.$this->option.a.'id='.$this->project->id); ?>">
	<fieldset >
		<?php echo $this->status; ?>
		<p class="submitarea">
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
				<a id="cancel-action" class="btn btn-cancel" href="<?php echo $this->url . '?a=1' .$subdirlink; ?>"><?php echo JText::_('Go back'); ?></a>
			<?php } ?>
		</p>
	</fieldset>
</form>
</div>