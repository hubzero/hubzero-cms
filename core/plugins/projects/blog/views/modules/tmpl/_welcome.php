<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->model->access('content'))
{
	return;
}

$max_s = 3;
$actual_s = count($this->suggestions) >= $max_s ? $max_s : count($this->suggestions);

if ($actual_s <= 1)
{
	return;
}
$i = 0;
?>
<?php if ($actual_s > 1) { ?>
	<div class="welcome">
		<p class="closethis"><a href="<?php echo Route::url('index.php?option=' . $this->option
		. '&alias=' . $this->model->get('alias') . '&active=feed') . '?c=1'; ?>"><?php echo Lang::txt('COM_PROJECTS_PROJECT_CLOSE_THIS'); ?></a></p>

		<h3><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_TO_PROJECT_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_TO').' '.stripslashes($this->model->get('title')).' '.Lang::txt('COM_PROJECTS_PROJECT').'!'; ?> </h3>
		<p><?php echo $this->model->access('owner') ? Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS_CREATOR') : Lang::txt('COM_PROJECTS_WELCOME_SUGGESTIONS'); ?></p>
		<div id="suggestions" class="suggestions">
			<?php foreach ($this->suggestions as $suggestion)
				{ $i++;
				  if ($i <= $max_s)
					{ ?>
				<div class="<?php echo $suggestion['class']; ?>">
					<p><a href="<?php echo $suggestion['url']; ?>"><?php echo $suggestion['text']; ?></a></p>
				</div>
			<?php }
			} ?>
			<div class="clear"></div>
		</div>
	</div>
<?php  } ?>
