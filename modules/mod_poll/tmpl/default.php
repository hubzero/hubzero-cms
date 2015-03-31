<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access'); ?>

<form id="<?php echo ($this->params->get('moduleclass_sfx')) ? $this->params->get('moduleclass_sfx') : 'poll' . rand(); ?>" method="post" action="<?php echo Route::url('index.php?option=com_poll'); ?>">
	<fieldset>
		<h4><?php echo $this->escape($poll->title); ?></h4>
		<ul class="poll">
	<?php for ($i = 0, $n = count($options); $i < $n; $i ++) : ?>
			<li class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
				<input type="radio" name="voteid" id="voteid<?php echo $options[$i]->id;?>" value="<?php echo $this->escape($options[$i]->id);?>" />
				<label for="voteid<?php echo $options[$i]->id; ?>" class="<?php echo $this->escape($tabclass_arr[$tabcnt]); ?><?php echo $this->params->get('moduleclass_sfx'); ?>">
					<?php echo $this->escape(str_replace('&#039;', "'", $options[$i]->text)); ?>
				</label>
			</li>
			<?php
				$tabcnt = 1 - $tabcnt;
			?>
	<?php endfor; ?>
		</ul>
		<p>
			<input type="submit" name="task_button" class="button" value="<?php echo Lang::txt('MOD_POLL_VOTE'); ?>" />
			 &nbsp;
			<a href="<?php echo Route::url('index.php?option=com_poll&view=poll&id=' . $this->escape($poll->slug)); ?>"><?php echo Lang::txt('MOD_POLL_RESULTS'); ?></a>
		</p>

		<input type="hidden" name="option" value="com_poll" />
		<input type="hidden" name="task" value="vote" />
		<input type="hidden" name="id" value="<?php echo $this->escape($poll->id); ?>" />
		<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>