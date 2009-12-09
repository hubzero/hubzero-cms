<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<div class="main section">
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('NORMALIZED_TAG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('DETAILS'); ?></h3>

			<label>
				<?php echo JText::_('TAG'); ?>
				<input type="text" name="raw_tag" value="<?php echo htmlentities(stripslashes($this->tag->raw_tag),ENT_COMPAT,'UTF-8'); ?>" size="38" />
			</label>

			<label>
				<?php echo JText::_('COL_ALIAS'); ?>
				<input type="text" name="alias" value="<?php echo htmlentities(stripslashes($this->tag->alias),ENT_COMPAT,'UTF-8'); ?>" size="38" />
			</label>

			<label>
				<input class="option" type="checkbox" name="minor_edit" value="1" /> 
				<strong><?php echo JText::_('ADMINISTRATION'); ?></strong>
			</label>
			<p class="hint"><?php echo JText::_('ADMINISTRATION_EXPLANATION'); ?></p>
	
			<label>
				<?php echo JText::_('DESCRIPTION'); ?>
				<textarea name="description" rows="10" cols="35"><?php echo stripslashes($this->tag->description); ?></textarea>
			</label>

			<input type="hidden" name="tag" value="<?php echo $this->tag->tag; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->tag->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
	</form>
</div><!-- / .main section -->