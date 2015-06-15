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

if ($this->rows) { ?>
	<table class="activity" id="wiki-list">
		<tbody>
		<?php
		$cls = 'even';
		foreach ($this->rows as $row)
		{
			$name = Lang::txt('WIKI_AUTHOR_UNKNOWN');
			$user = User::getInstance($row->created_by);
			if (is_object($user) && $user->get('name'))
			{
				$name = $user->get('name');
			}

			if ($row->version > 1)
			{
				$t = Lang::txt('WIKI_EDITED');
				$c = 'wiki-edited';
			}
			else
			{
				$t = Lang::txt('WIKI_CREATED');
				$c = 'wiki-created';
			}

			$cls = ($cls == 'even') ? 'odd' : 'even';
			?>
			<tr class="<?php echo $cls; ?>">
				<th scope="row"><span class="<?php echo $c; ?>"><?php echo $t; ?></span></th>
				<td><a href="<?php echo Route::url('index.php?option='.$this->option.'&pagename='.$row->pagename.'&scope='.$row->scope); ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td class="author"><a href="<?php echo Route::url('index.php?option=com_members&id='.$row->created_by); ?>"><?php echo $name; ?></a></td>
				<td class="date"><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_GROUPS_WIKI_NO_RESULTS_FOUND'); ?></p>
<?php }