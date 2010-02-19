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
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo $this->getError(); ?></p>
<?php } ?>
	<form name="hubForm" id="hubForm" method="post" action="index.php" class="contrib">
		<div class="explaination">
			<p class="warning"><?php echo JText::_('Canceling a contribution will permanently delete any stored description, linked files, and tags. These cannot be recovered.'); ?><p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('Contribution'); ?></h3>
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="discard" />
			<input type="hidden" name="step" value="2" />
			
			<p><strong><?php echo stripslashes($this->row->title); ?></strong><br />
			<?php echo $this->row->typetitle; ?></p>
			
			<label><input type="checkbox" name="confirm" value="confirmed" class="option" /> <?php echo JText::_('Confirm discard'); ?></label>
		</fieldset><div class="clear"></div>
		
		<div class="submit">
			<input type="submit" value="<?php echo JText::_('Delete'); ?>" />
		</div>
	</form>
</div><!-- / .main section -->