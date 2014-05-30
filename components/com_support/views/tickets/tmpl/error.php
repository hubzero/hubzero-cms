<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<div id="report-response">
	<div>
		<p><?php echo JText::_('COM_SUPPORT_ERROR_PROCESSING_FORM'); ?></p>
		<p><a href="javascript:HUB.Modules.ReportProblems.reshowForm();" title="<?php echo JText::_('COM_SUPPORT_EDIT_REPORT'); ?>"><?php echo JText::_('COM_SUPPORT_EDIT_REPORT'); ?></a></p>
	</div>
	<h3><?php echo JText::_('COM_SUPPORT_ERROR'); ?></h3>
	<p><?php echo JText::_('COM_SUPPORT_ERROR_PROCESSING_DESCRIPTION'); ?></p>
<?php if ($this->getError()) { echo '<p>' . $this->getError() . '</p>'; } ?>
</div>

<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>