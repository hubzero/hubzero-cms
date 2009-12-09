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
<?php
if ($this->report && !$this->getError()) {
	$name = JText::_('ANONYMOUS');
	if ($this->report->anon == 0) {
		$xuser =& JUser::getInstance($this->report->author);
		$name = JText::_('UNKNOWN');
		if (is_object($xuser)) {
			$name = $xuser->get('name');
		}
	}
?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('REPORT_ABUSE_EXPLANATION'); ?></p>
			<p><?php echo JText::_('REPORT_ABUSE_DESCRIPTION_HINT'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('REPORT_ABUSE'); ?></h3>
	
			<div class="abuseitem">
				<h4><?php 
	 				echo ($this->report->href) ? '<a href="'.$this->report->href.'">': '';
	 				echo ucfirst($this->cat).' by ';
	 				echo ($this->report->anon != 0) ? JText::_('ANONYMOUS') : $name;
	 				echo ($this->report->href) ? '</a>': ''; 
				?></h4>
				<?php echo ($this->report->subject) ? t.t.'<p><strong>'.stripslashes($this->report->subject).'</strong></p>'.n : ''; ?>
				<p><?php echo stripslashes($this->report->text); ?></p>
			</div>
	
			<p class="multiple-option">
				<label class="option"><input type="radio" class="option" name="subject" id="subject1" value="<?php echo JText::_('OFFENSIVE_CONTENT'); ?>" checked="checked" /> <?php echo JText::_('OFFENSIVE_CONTENT'); ?></label>
				<label class="option"><input type="radio" class="option" name="subject" id="subject2" value="<?php echo JText::sprintf('STUPID',$this->cat); ?>" /> <?php echo JText::sprintf('STUPID',$this->cat); ?></label>
				<label class="option"><input type="radio" class="option" name="subject" id="subject3" value="<?php echo JText::_('SPAM'); ?>" /> <?php echo JText::_('SPAM'); ?></label>
				<label class="option"><input type="radio" class="option" name="subject" id="subject4" value="<?php echo JText::_('OTHER'); ?>" /> <?php echo JText::_('OTHER'); ?></label>
			</p>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="savereport" />
			<input type="hidden" name="category" value="<?php echo $this->report->parent_category; ?>" />
			<input type="hidden" name="referenceid" value="<?php echo $this->refid; ?>" />
			<input type="hidden" name="link" value="<?php echo $this->report->href; ?>" />
			<label>
				<?php echo JText::_('REPORT_ABUSE_DESCRIPTION'); ?>
				<textarea name="report" rows="10" cols="50"></textarea>
			</label>
		</fieldset>
		<p class="submit"><input type="submit" value="<?php echo JText::_('SUBMIT'); ?>" /></p>
	</form>
	<div class="clear"></div>
<?php
} else {
	if ($this->getError()) { 
?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php
	} else {
?>
		<p class="warning"><?php echo JText::_('ERROR_NO_INFO_ON_REPORTED_ITEM'); ?></p>
<?php
	}
}
?>
</div><!-- / .main section -->