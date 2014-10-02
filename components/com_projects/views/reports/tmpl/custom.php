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

$this->css('reports')
	->css('calendar')
	->js('reports');

// Incoming
$data   = JRequest::getVar( 'data', array(), 'post', 'array' );
$from   = JRequest::getVar( 'fromdate', JHTML::_('date', JFactory::getDate('-1 month')->toSql(), 'Y-m') );
$to     = JRequest::getVar( 'todate', JHTML::_('date', JFactory::getDate()->toSql(), 'Y-m') );
$filter = JRequest::getVar( 'searchterm', '');

?>
<header id="content-header" class="reports">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section custom-reports" id="custom-reports">
	<div class="status-msg">
	<?php
		// Display error or success message
		if ($this->getError()) {
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if ($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
	<div class="report-block">
		<h3><?php echo JText::_('Publications Report'); ?></h3>
		<form id="reportForm" method="post" action="">
			<fieldset>
				<input type="hidden"  name="controller" value="reports" />
				<input type="hidden"  name="task" value="generate" />
				<input type="hidden"  name="no_html" value="1" />
			</fieldset>
			<div class="report-content">
				<div class="groupblock">
					<h6><?php echo JText::_('Download publication data:'); ?></h6>
					<label>
						<?php $ph = JHTML::_('date', JFactory::getDate('-1 month')->toSql(), 'Y-m'); ?>
						<?php echo JText::_('From'); ?>: <input type="text" value="<?php echo $from; ?>" id="from-date" name="fromdate" placeholder="<?php echo $ph; ?>" maxlength="7" />
					</label>
					<label>
						<?php $ph = JHTML::_('date', JFactory::getDate()->toSql(), 'Y-m'); ?>
						<?php echo JText::_('To'); ?>: <input type="text" value="<?php echo $to; ?>" id="to-date"  name="todate" placeholder="<?php echo $ph; ?>" maxlength="7" />
					</label>
				</div>
				<div class="groupblock">
					<div class="block">
						<label><?php echo JText::_('Filter by tag'); ?>:
						<input type="text" value="<?php echo $filter; ?>" id="searchterm" name="searchterm" placeholder="<?php echo JText::_('Enter a tag'); ?>" />
						</label>
					</div>
				</div>
				<h6><?php echo JText::_('Include the following information:'); ?></h6>
				<div class="groupblock element-choice">
					<div class="columns three first">
						<label class="block">
							<input type="checkbox" name="data[]" value="id" checked="checked" /> <?php echo JText::_('Publication ID'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="title" checked="checked" /> <?php echo JText::_('Publication title'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="author" checked="checked" /> <?php echo JText::_('First author'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="version" checked="checked" /> <?php echo JText::_('Version label'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="doi" checked="checked" /> <?php echo JText::_('DOI url'); ?>
						</label>
					</div>
					<div class="columns three second">
						<label class="block">
							<input type="checkbox" name="data[]" value="downloads" checked="checked" /> <?php echo JText::_('Number of downloads'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="views" checked="checked" /> <?php echo JText::_('Number of page views'); ?>
						</label>
						<label class="block">
							<input type="checkbox" name="data[]" value="citations" checked="checked" /> <?php echo JText::_('Number of citations'); ?>
						</label>
					</div>
					<div class="clear"></div>
				</div>
				<p class="submitarea">
					<span class="button-wrapper icon-download">
						<input type="submit" class="btn active btn-primary icon-download" value="<?php echo JText::_('Download report (CSV)'); ?>"  />
					</span>
				</p>
			</div>
		</form>
	</div>
</section>