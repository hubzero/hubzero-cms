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

//$ba = new BulletinboardAsset(JFactory::getDBO());
//$assets = $ba->getRecords(array('bulletin_id' => $this->row->id, 'limit' => 50, 'start' => 0));

$path = DS . trim($this->params->get('filepath', '/site/bulletins'), DS) . DS . $this->row->id;
$base = 'index.php?option=' . $this->option . '&gid=' . $this->group->get('cn') . '&active=' . $this->name;

if ($this->assets)
{
?>
		<ul class="file-list">
<?php
		foreach ($this->assets as $asset)
		{
			if ($asset->bulletin_id != $this->row->id)
			{
				continue;
			}
?>
				<li>
					<a href="<?php echo $path . DS . ltrim($asset->filename, DS); ?>">
						<?php echo $asset->filename; ?>
					</a>
					<span class="file-meta">
						<span class="file-size">
							<?php echo Hubzero_View_Helper_Html::formatSize(filesize(JPATH_ROOT . $path . DS . ltrim($asset->filename, DS))); ?>
						</span>
				<?php if ($asset->description) { ?>
						<span class="file-description">
							<?php echo Hubzero_View_Helper_Html::formatSize(filesize(JPATH_ROOT . $path . DS . ltrim($asset->filename, DS))); ?>
						</span>
				<?php } ?>
					</span>
				</li>
<?php
		}
?>
		</ul>
<?php
}
?>
<?php if ($this->row->description) { ?>
		<p class="description">
			<?php echo $this->escape(stripslashes($this->row->description)); ?>
		</p>
<?php } ?>