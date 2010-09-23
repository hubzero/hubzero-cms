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
<?php if ($this->no_html) { ?>
<!-- <html>
<head>
</head>
<body onload="window.top.window.HUB.ReportProblem.hideTimer();"> -->
<div id="report-response">
	<div>
		<p><?php echo JText::_('COM_FEEDBACK_YOUR_TICKET'); ?> # <span><a href="<?php echo JRoute::_('index.php?option=com_support&task=ticket&id='.$this->ticket); ?>" title="View ticket"><?php echo $this->ticket; ?></a></span></p>
		<p><button onclick="javascript:HUB.ReportProblem.resetForm();" title="<?php echo JText::_('COM_FEEDBACK_NEW_REPORT'); ?>"><?php echo JText::_('COM_FEEDBACK_NEW_REPORT'); ?></button></p>
	</div>
	<p>
		<?php echo JText::_('COM_FEEDBACK_TROUBLE_THANKS'); ?><br /><br />
		<?php echo JText::_('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?>
	</p>
</div>
<script type="text/javascript">window.top.window.HUB.ReportProblem.hideTimer();</script>
<!-- </body>
</html> -->
<?php } else { ?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	<p><?php echo JText::_('COM_FEEDBACK_TROUBLE_THANKS'); ?></p>
	<p class="information"><?php echo JText::_('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?></p>
<?php if ($this->ticket) { ?>
	<p><?php echo JText::sprintf('COM_FEEDBACK_TROUBLE_TICKET_REFERENCE',$this->ticket); ?></p>
<?php } ?>
</div><!-- / .main section -->
<?php } ?>