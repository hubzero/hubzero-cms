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

$no_html = JRequest::getInt('no_html', 0);

$assets = $this->item->assets();
if ($assets->total() > 0) 
{ 
	$i = 0;
	foreach ($assets as $asset)
	{
?>
		<p class="item-asset">
			<span class="asset-file">
			<?php if ($asset->get('type') == 'link') { ?>
				<input type="text" name="assets[<?php echo $i; ?>][filename]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" placeholder="http://" />
			<?php } else { ?>
				<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>
				<input type="hidden" name="assets[<?php echo $i; ?>][filename]" value="<?php echo $this->escape(stripslashes($asset->get('filename'))); ?>" />
			<?php } ?>
			</span>
			<span class="asset-description">
				<input type="hidden" name="assets[<?php echo $i; ?>][type]" value="<?php echo $asset->get('type'); ?>" />
				<input type="hidden" name="assets[<?php echo $i; ?>][id]" value="<?php echo $asset->get('id'); ?>" />
				<a class="delete" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=delete&id=' . $asset->get('id') . '&no_html=' . $no_html); ?>" title="<?php echo JText::_('Delete this asset'); ?>">
					delete
				</a>
				<!-- <input type="text" name="assets[<?php echo $i; ?>][description]" size="35" value="<?php echo $this->escape(stripslashes($asset->get('description'))); ?>" placeholder="Brief description" /> -->
			</span>
		</p>
<?php 
		$i++;
	}
}