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

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-search search btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=search'); ?>">
				<span><?php echo JText::_('COM_ANSWERS_ALL_QUESTIONS'); ?></span>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<div class="section-inner">
		<?php foreach ($this->notifications as $notification) { ?>
			<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm">
			<div class="explaination">
				<p><?php echo JText::_('COM_ANSWERS_BE_POLITE'); ?></p>
				<?php if ($this->config->get('banking')) { ?>
					<p class="help">
						<strong><?php echo JText::_('COM_ANSWERS_WHAT_IS_REWARD'); ?></strong><br />
						<?php echo JText::_('COM_ANSWERS_EXPLAINED_MARKET_VALUE'); ?> <a href="<?php echo $this->config->get('infolink'); ?>"><?php echo JText::_('COM_ANSWERS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_ANSWERS_ABOUT_POINTS'); ?>
					</p>
				<?php } ?>
			</div><!-- / .explaination -->
			<fieldset>
				<legend><?php echo JText::_('COM_ANSWERS_YOUR_QUESTION'); ?></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
				<input type="hidden" name="task" value="saveq" />

				<?php echo JHTML::_('form.token'); ?>

				<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->question->get('id', 0)); ?>" />
				<input type="hidden" name="fields[funds]" value="<?php echo $this->escape($this->funds); ?>" />
				<input type="hidden" name="fields[email]" value="1" />
				<input type="hidden" name="fields[state]" value="0" />

				<label for="field-anonymous">
					<input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous" value="1" />
					<?php echo JText::_('COM_ANSWERS_POST_QUESTION_ANON'); ?>
				</label>

				<label>
					<?php echo JText::_('COM_ANSWERS_TAGS'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
					<?php echo $this->autocompleter('tags', 'tags', $this->escape($this->tag), 'actags'); ?>
				</label>

				<label for="field-subject">
					<?php echo JText::_('COM_ANSWERS_ASK_ONE_LINER'); ?>: <span class="required"><?php echo JText::_('COM_ANSWERS_REQUIRED'); ?></span><br />
					<input type="text" name="fields[subject]" id="field-subject" value="<?php echo $this->escape($this->question->get('subject', '')); ?>" />
				</label>

				<label for="field-question">
					<?php echo JText::_('COM_ANSWERS_ASK_DETAILS'); ?>:<br />
					<?php
						echo $this->editor(
							'fields[question]', // name
							$this->question->content('raw'), // content
							35,  // cols
							10,   // rows
							'field-question'   // id
						);
					?>
				</label>

				<?php if ($this->config->get('banking')) { ?>
					<label for="field-reward">
						<?php echo JText::_('COM_ANSWERS_ASSIGN_REWARD'); ?>:<br />
						<input type="text" name="fields[reward]" id="field-reward" value="" size="5" <?php if ($this->funds <= 0) { echo 'disabled="disabled" '; } ?>/>
						<?php echo JText::_('COM_ANSWERS_YOU_HAVE'); ?> <strong><?php echo $this->escape($this->funds); ?></strong> <?php echo JText::_('COM_ANSWERS_POINTS_TO_SPEND'); ?>
					</label>
				<?php } else { ?>
					<input type="hidden" name="fields[reward]" value="0" />
				<?php } ?>
			</fieldset>
			<div class="clear"></div>

			<p class="submit">
				<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_ANSWERS_SUBMIT'); ?>" />

				<a class="btn btn-secondary" href="<?php echo $this->question->exists() ? JRoute::_($this->question->link()) : JRoute::_('index.php?option=' . $this->option); ?>">
					<?php echo JText::_('COM_ANSWERS_CANCEL'); ?>
				</a>
			</p>
		</form>
	</div><!-- / .section-inner -->
</section><!-- / .main section -->
