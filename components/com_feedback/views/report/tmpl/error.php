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
<div>
	<p><?php echo JText::_('COM_FEEDBACK_ERROR_PROCESSING_FORM'); ?></p>
	<p><a href="javascript:HUB.ReportProblem.reshowForm();" title="<?php echo JText::_('COM_FEEDBACK_EDIT_REPORT'); ?>"><?php echo JText::_('COM_FEEDBACK_EDIT_REPORT'); ?></a></p>
</div>
<h3><?php echo JText::_('COM_FEEDBACK_ERROR'); ?></h3>
<p><?php echo JText::_('COM_FEEDBACK_ERROR_PROCESSING_DESCRIPTION'); ?></p>