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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

\Hubzero\Document\Assets::addSystemScript('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.fancyselect');
\Hubzero\Document\Assets::addSystemStylesheet('jquery.ui.css');

$this->css()
     ->css('records')
     ->js('records')
     ->js('time');

$base = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="com_time_container">
	<?php $this->view('menu', 'shared')->display(); ?>
	<section class="com_time_content com_time_records">
		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-reply btn" href="<?php echo JRoute::_($base . $this->start); ?>">
						<?php echo JText::_('COM_TIME_RECORDS_ALL_RECORDS'); ?>
					</a>
				</li>
			</ul>
		</div>
		<div class="container">
			<?php if (count($this->getErrors()) > 0) : ?>
				<?php foreach ($this->getErrors() as $error) : ?>
				<p class="error"><?php echo $this->escape($error); ?></p>
				<?php endforeach; ?>
			<?php endif; ?>
			<form action="<?php echo JRoute::_($base . '&task=save'); ?>" method="post">
				<div class="grouping" id="uname-group">
					<label for="uname"><?php echo JText::_('COM_TIME_RECORDS_USER'); ?>:</label>
					<?php if (isset($this->subordinates)) : ?>
						<?php echo $this->subordinates; ?>
					<?php else : ?>
						<?php echo htmlentities(stripslashes($this->row->uname), ENT_QUOTES); ?>
						<input type="hidden" name="records[user_id]" value="<?php echo $this->row->user_id; ?>" />
					<?php endif; ?>
				</div>

				<div class="grouping clearfix" id="time-group">
					<label for="time" style="float:left;"><?php echo JText::_('COM_TIME_RECORDS_TIME'); ?>:</label>
					<div style="width: 75px; float:left; margin-right: 5px; margin-left: 5px"><?php echo $this->htimelist; ?></div>
					<div style="width: 75px; float:left;"><?php echo $this->mtimelist; ?></div>
				</div>

				<div class="grouping clearfix" id="date-group">
					<label for="date"><?php echo JText::_('COM_TIME_RECORDS_DATE'); ?>:</label>
					<input type="text" name="records[date]" id="datepicker" class="hadDatepicker" value="<?php echo htmlentities(stripslashes($this->row->date), ENT_QUOTES); ?>" size="10" tabIndex="3" />
				</div>

				<div class="grouping" id="hub-group">
					<label for="hub"><?php echo JText::_('COM_TIME_RECORDS_HUB'); ?>:</label>
					<?php echo $this->hubslist; ?>
				</div>

				<div class="grouping" id="task-group">
					<label for="task"><?php echo JText::_('COM_TIME_RECORDS_TASK'); ?>:</label>
					<?php echo $this->tasklist; ?>
				</div>

				<div class="grouping" id="description-group">
					<label for="description"><?php echo JText::_('COM_TIME_RECORDS_DESCRIPTION'); ?>:</label>
					<textarea name="records[description]" id="description" rows="6" cols="50" tabIndex="6"><?php echo htmlentities(stripslashes($this->row->description), ENT_QUOTES); ?></textarea>
				</div>

				<input type="hidden" name="records[id]" value="<?php echo $this->row->id; ?>" />

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_TIME_RECORDS_SUBMIT'); ?>" tabIndex="7" />
					<a href="<?php echo JRoute::_($base . $this->start); ?>">
						<button type="button" class="btn btn-secondary">
							<?php echo JText::_('COM_TIME_RECORDS_CANCEL'); ?>
						</button>
					</a>
				</p>
			</form>
		</div>
	</section>
</div>