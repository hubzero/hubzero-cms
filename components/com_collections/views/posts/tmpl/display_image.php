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

$item = $this->row->item();

$path = DS . trim($this->params->get('filepath', '/site/collections'), DS) . DS . $item->get('id');
$base = 'index.php?option=' . $this->option;

$assets = $item->assets();

if ($assets->total() > 0)
{
	//$assets->rewind();
	$first = $assets->fetch('first'); //array_shift($assets);

	list($originalWidth, $originalHeight) = getimagesize(JPATH_ROOT . $path . DS . ltrim($first->get('filename'), DS));
	$ratio = $originalWidth / $originalHeight;
?>
		<div class="holder">
			<a rel="lightbox" href="<?php echo $path . DS . ltrim($first->get('filename'), DS); ?>" class="img-link">
				<img src="<?php echo $path . DS . ltrim($first->get('filename'), DS); ?>" alt="<?php echo ($first->get('description')) ? $this->escape(stripslashes($first->get('description'))) : ''; ?>" class="img" style="height: <?php echo round(300 / $ratio, 0, PHP_ROUND_HALF_UP); ?>px;" />
			</a>
		</div>
<?php
	if ($assets->total() > 1)
	{
?>
		<div class="gallery">
<?php
		foreach ($assets as $key => $asset)
		{
			if ($key == 0)
			{
				continue;
			}
?>
			<a rel="lightbox" href="<?php echo $path . DS . ltrim($asset->get('filename'), DS); ?>" class="img-link">
				<img src="<?php echo $path . DS . ltrim($asset->get('filename'), DS); ?>" alt="<?php echo ($asset->get('description')) ? $this->escape(stripslashes($asset->get('description'))) : ''; ?>" class="img" />
			</a>
<?php
		}
?>
			<div class="clearfix"></div>
		</div>
<?php
	}
}
?>
<?php if ($item->get('description') || $this->row->get('description')) { ?>
		<p class="description">
			<?php echo ($this->row->get('description')) ? $this->escape(stripslashes($this->row->get('description'))) : $this->escape(stripslashes($item->get('description'))); ?>
		</p>
<?php } ?>