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

$this->css()
     ->js();

if ($this->wishlist->exists())
{
	$login = JText::_('COM_WISHLIST_UNKNOWN');

	// what is submitter name?
	if ($this->task == 'editwish')
	{
		$login = $this->wish->proposer('username');
	}

	$this->wish->set('about', preg_replace('/<br\\s*?\/??>/i', '', $this->wish->get('about')));
?>
	<header id="content-header">
		<h2><?php echo $this->escape($this->title); ?></h2>

		<div id="content-header-extra">
			<ul id="useroptions">
				<li class="last">
					<a class="icon-wish nav_wishlist btn" href="<?php echo JRoute::_($this->wishlist->link()); ?>">
						<?php echo JText::_('COM_WISHLIST_WISHES_ALL'); ?>
					</a>
				</li>
			</ul>
		</div><!-- / #content-header-extra -->
	</header>

	<section class="main section">
		<form id="hubForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

			<div class="explaination">
				<p><?php echo JText::_('COM_WISHLIST_TEXT_ADD_WISH'); ?></p>
				<?php if ($this->banking && $this->task != 'editwish') { ?>
					<p class="help">
						<strong><?php echo JText::_('COM_WISHLIST_WHAT_IS_REWARD'); ?></strong><br />
						<?php echo JText::_('COM_WISHLIST_WHY_ADDBONUS'); ?> <a href="<?php echo $this->infolink; ?>"><?php echo JText::_('COM_WISHLIST_LEARN_MORE'); ?></a> <?php echo JText::_('COM_WISHLIST_ABOUT_POINTS'); ?>.
					</p>
				<?php } ?>
			</div><!-- / .aside -->
			<fieldset>
				<legend><?php echo JText::_('Details'); ?></legend>

			<?php if ($this->task == 'editwish') { ?>
				<label for="field-by">
					<?php echo JText::_('COM_WISHLIST_WISH_PROPOSED_BY'); ?>: <span class="required"><?php echo JText::_('COM_WISHLIST_REQUIRED'); ?></span>
					<input name="by" maxlength="50" id="field-by" type="text" value="<?php echo $this->escape($login); ?>" />
				</label>
			<?php } ?>

				<label for="field-anonymous">
					<input class="option" type="checkbox" name="anonymous" id="field-anonymous" value="1" <?php echo ($this->wish->get('anonymous')) ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_WISHLIST_WISH_POST_ANONYMOUSLY'); ?>
				</label>

			<?php if ($this->wishlist->access('manage') && $this->wishlist->isPublic()) { // list owner ?>
					<label for="field-private">
					<input class="option" type="checkbox" name="private" id="field-private" value="1" <?php echo ($this->wish->get('private')) ? 'checked="checked"' : ''; ?>/>
					<?php echo JText::_('COM_WISHLIST_WISH_MAKE_PRIVATE'); ?>
				</label>
			<?php } ?>

				<input type="hidden" name="proposed_by" value="<?php echo $this->escape($this->wish->get('proposed_by')); ?>" />
				<input type="hidden" name="task" value="savewish" />
				<input type="hidden" name="wishlist" value="<?php echo $this->escape($this->wishlist->get('id')); ?>" />
				<input type="hidden" name="status" value="<?php echo $this->escape($this->wish->get('status')); ?>" />
				<input type="hidden" name="id" value="<?php echo $this->escape($this->wish->get('id')); ?>" />

				<?php echo JHTML::_('form.token'); ?>

				<label for="subject">
					<?php echo JText::_('COM_WISHLIST_SUMMARY_OF_WISH'); ?> <span class="required"><?php echo JText::_('COM_WISHLIST_REQUIRED'); ?></span>
					<input name="subject" maxlength="120" id="subject" type="text" value="<?php echo $this->escape(stripslashes($this->wish->get('subject'))); ?>" />
				</label>

				<label for="field_about">
					<?php echo JText::_('COM_WISHLIST_WISH_EXPLAIN_IN_DETAIL'); ?>:
					<?php
						echo JFactory::getEditor()->display('about', $this->escape($this->wish->content('raw')), '', '', 35, 10, false, 'field_about', null, null, array('class' => 'minimal no-footer'));
					?>
				</label>

				<label>
					<?php echo JText::_('COM_WISHLIST_WISH_ADD_TAGS'); ?>: <br />
					<?php
					// Tag editor plug-in
					JPluginHelper::importPlugin( 'hubzero' );
					$dispatcher = JDispatcher::getInstance();
					$tf = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags','', $this->wish->tags('string'))) );
					if (count($tf) > 0) {
						echo $tf[0];
					} else { ?>
						<textarea name="tags" id="tags-men" rows="6" cols="35"><?php echo $this->escape($this->wish->tags('string')); ?></textarea>
					<?php } ?>
				</label>

			<?php if ($this->banking && $this->task != 'editwish') { ?>
				<label for="field-reward">
					<?php echo JText::_('COM_WISHLIST_ASSIGN_REWARD'); ?>:<br />
					<input type="text" name="reward" id="field-reward" value="" size="5"<?php if ($this->funds <= 0 ) { echo ' disabled="disabled"'; } ?> />
					<span class="subtext"><?php echo JText::_('COM_WISHLIST_YOU_HAVE'); ?> <strong><?php echo $this->escape($this->funds); ?></strong> <?php echo JText::_('COM_WISHLIST_POINTS_TO_SPEND'); ?>.</span>
				</label>
				<input type="hidden"  name="funds" value="<?php echo $this->escape($this->funds); ?>" />
			<?php } ?>
			</fieldset>
			<div class="clear"></div>

			<p class="submit">
				<input type="submit" class="btn btn-success" value="<?php echo JText::_('COM_WISHLIST_FORM_SUBMIT'); ?>" />
				<a class="btn btn-secondary" href="<?php echo $this->wish->link(); ?>">
					<?php echo JText::_('COM_WISHLIST_CANCEL'); ?>
				</a>
			</p>
		</form>
	</section><!-- / .main section -->
<?php } else { ?>
	<section class="main section">
		<p class="error"><?php echo JText::_('COM_WISHLIST_ERROR_WISHLIST_NOT_FOUND'); ?></p>
	</section><!-- / .main section -->
<?php } ?>