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

$base = rtrim(JURI::base(true), '/');

$this->css();
?>
	<div id="attachments">
		<form action="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=<?php echo $this->controller; ?>&amp;task=upload" id="adminForm" method="post" enctype="multipart/form-data">
			<fieldset>
				<div id="themanager" class="manager">
					<iframe src="<?php echo $base; ?>/index.php?option=<?php echo $this->option; ?>&amp;tmpl=component&amp;controller=<?php echo $this->controller; ?>&amp;task=list&amp;scope=<?php echo urlencode($this->scope); ?>&amp;id=<?php echo $this->id; ?>" name="imgManager" id="imgManager" width="98%" height="180"></iframe>
				</div>
			</fieldset>
			<fieldset>
				<p><input type="file" name="upload" id="upload" /></p>
				<p><input type="submit" value="<?php echo JText::_('COM_BLOG_UPLOAD'); ?>" /></p>

				<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
				<input type="hidden" name="task" value="upload" />
				<input type="hidden" name="scope" value="<?php echo $this->escape($this->scope); ?>" />
				<input type="hidden" name="id" value="<?php echo $this->escape($this->id); ?>" />
				<input type="hidden" name="tmpl" value="component" />
			</fieldset>
		</form>
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
	</div>
