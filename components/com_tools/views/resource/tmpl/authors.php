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
?>
<div class="explaination">
	<h4><?php echo JText::_('COM_TOOLS_AUTHORS_NO_LOGIN'); ?></h4>
	<p><?php echo JText::_('COM_TOOLS_AUTHORS_NO_LOGIN_EXPLANATION'); ?></p>
</div>
<fieldset>
	<legend><?php echo JText::_('COM_TOOLS_AUTHORS_AUTHORS'); ?></legend>
	<div class="field-wrap">
		<iframe name="authors" id="authors" src="index.php?option=<?php echo $this->option; ?>&amp;controller=authors&amp;rid=<?php echo $this->row->id; ?>&amp;tmpl=component&amp;version=<?php echo $this->version; ?>" width="100%" height="400" frameborder="0"></iframe>
	</div>
</fieldset><div class="clear"></div>