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

if ($this->row instanceof \Components\Collections\Models\Collection)
{
	$collection = $this->row;
}
else
{
	$collection = \Components\Collections\Models\Collection::getInstance($this->row->item()->get('object_id'));
	if ($this->row->get('description'))
	{
		$collection->set('description', $this->row->get('description'));
	}
}
?>
		<h4<?php if ($collection->get('access', 0) == 4) { echo ' class="private"'; } ?>>
			<a href="<?php echo Route::url($collection->link()); ?>">
				<?php echo $this->escape(stripslashes($collection->get('title'))); ?>
			</a>
		</h4>
		<div class="description">
			<?php echo $collection->description('parsed'); ?>
		</div>
		<?php /* <table>
			<tbody>
				<tr>
					<td>
						<strong><?php echo $collection->count('file'); ?></strong> <span class="post-type file"><?php echo Lang::txt('files'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('collection'); ?></strong> <span class="post-type collection"><?php echo Lang::txt('collections'); ?></span>
					</td>
					<td>
						<strong><?php echo $collection->count('link'); ?></strong> <span class="post-type link"><?php echo Lang::txt('links'); ?></span>
					</td>
				</tr>
			</tbody>
		</table> */ ?>