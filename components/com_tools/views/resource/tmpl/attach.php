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
defined('_JEXEC') or die( 'Restricted access' );

$allowupload = ($this->version=='current' or !$this->status['published']) ? 1 : 0;
?>
	<div class="explaination">
		<h4><?php echo JText::_('COM_TOOLS_ATTACH_WHAT_ARE_ATTACHMENTS'); ?></h4>
		<p><?php echo JText::_('COM_TOOLS_ATTACH_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('COM_TOOLS_ATTACH_ATTACHMENTS'); ?></legend>
		<div class="field-wrap">
			<iframe width="100%" height="200" frameborder="0" name="attaches" id="attaches" src="index.php?option=<?php echo $this->option; ?>&amp;controller=attachments&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;type=7&amp;allowupload=<?php echo $allowupload; ?>"></iframe>
		</div>
	</fieldset><div class="clear"></div>

	<div class="explaination">
		<h4><?php echo JText::_('COM_TOOLS_ATTACH_WHAT_ARE_SCREENSHOTS'); ?></h4>
		<p><?php echo JText::_('COM_TOOLS_ATTACH_SCREENSHOTS_EXPLANATION'); ?></p>
	</div>
	<fieldset>
		<legend><?php echo JText::_('COM_TOOLS_ATTACH_SCREENSHOTS'); ?></legend>
		<div class="field-wrap">
			<iframe width="100%" height="400" frameborder="0" name="screens" id="screens" src="index.php?option=<?php echo $this->option; ?>&amp;controller=screenshots&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;version=<?php echo $this->version; ?>"></iframe>
		</div>
	</fieldset><div class="clear"></div>