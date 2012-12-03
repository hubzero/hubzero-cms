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

$board = new BulletinboardBoard(JFactory::getDBO());
$board->load($this->row->object_id);

$counts = $board->getPostTypeCount();

switch ($board->object_type)
{
	case 'member':
		$url = 'index.php?option=com_members&id=' . $board->object_id . '&active=bulletinboard&task=boards/' . $board->id;
	break;
	
	case 'group':
		ximport('Hubzero_Group');
		$group = new Hubzero_Group();
		$group->read($board->object_id);
		$url = 'index.php?option=com_groups&gid=' . $group->get('cn') . '&active=bulletinboard&scope=boards/' . $board->id;
	break;
}
?>
		<h4>
			<a href="<?php echo JRoute::_($url); ?>">
				<?php echo ($board->title) ? $this->escape(stripslashes($board->title)) : $this->escape(stripslashes($this->row->title)); ?>
			</a>
		</h4>
		<p class="description">
			<?php echo ($this->row->description) ? $this->escape(stripslashes($this->row->description)) : $this->escape(stripslashes($board->description)); ?>
		</p>
		<table summary="Board content counts">
			<tbody>
				<tr>
					<td>
						<strong><?php echo (isset($counts['image'])) ? $counts['image'] : 0; ?></strong> <span class="post-type image">images</span>
					</td>
					<td>
						<strong><?php echo (isset($counts['file'])) ? $counts['file'] : 0; ?></strong> <span class="post-type file">files</span>
					</td>
					<td>
						<strong><?php echo (isset($counts['text'])) ? $counts['text'] : 0; ?></strong> <span class="post-type text">texts</span>
					</td>
					<td>
						<strong><?php echo (isset($counts['link'])) ? $counts['link'] : 0; ?></strong> <span class="post-type link">links</span>
					</td>
				</tr>
			</tbody>
		</table>