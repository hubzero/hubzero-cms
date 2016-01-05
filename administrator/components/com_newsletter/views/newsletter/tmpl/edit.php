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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->js()
	 ->js('jquery.formwatcher', 'system');

//set title
$text = ($this->task == 'edit' ? JText::_('COM_NEWSLETTER_EDIT') : JText::_('COM_NEWSLETTER_NEW'));
JToolBarHelper::title(JText::_('COM_NEWSLETTER') . ': ' . $text, 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::apply();
if ($this->newsletter->id)
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

//primary and secondary stories
$primary = $this->newsletter_primary;
$secondary = $this->newsletter_secondary;

// Instantiate the sliders object
//jimport('joomla.html.pane');
//$tabs = JPane::getInstance('sliders');

jimport('joomla.html.editor');
$editor = JEditor::getInstance();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	submitform( pressbutton );
}
</script>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="index.php" method="post" name="adminForm" id="item-form" data-formwatcher-message='You are now leaving this page to add stories and your current changes have not been saved. Click "Stay on Page" and then save the newsletter first before proceeding to add stories.'>
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_DETAILS'); ?></legend>

			<div class="input-wrap">
				<label for="newsletter-name"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_NAME'); ?>:</label>
				<input type="text" name="newsletter[name]" id="newsletter-name" value="<?php echo $this->escape($this->newsletter->name); ?>" />
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_ALIAS_HINT'); ?>">
				<label for="newsletter-alias"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_ALIAS'); ?>:</label>
				<input type="text" name="newsletter[alias]" id="newsletter-alias" value="<?php echo $this->escape($this->newsletter->alias); ?>" />
				<span class="hint"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_ALIAS_HINT'); ?></span>
			</div>

			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="newsletter-issue"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_ISSUE'); ?>:</label>
					<input type="text" name="newsletter[issue]" id="newsletter-issue" value="<?php echo $this->escape($this->newsletter->issue); ?>" />
				</div>
			</div>
			<div class="col width-50 fltlft">
				<div class="input-wrap">
					<label for="newsletter-type"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_FORMAT'); ?>:</label>
					<select name="newsletter[type]" id="newsletter-type">
						<option value="html" <?php if ($this->newsletter->type == 'html') : ?>selected="selected"<?php endif; ?>>
							<?php echo JText::_('COM_NEWSLETTER_FORMAT_HTML'); ?>
						</option>
						<option value="plain" <?php if ($this->newsletter->type == 'plain') : ?>selected="selected"<?php endif; ?>>
							<?php echo JText::_('COM_NEWSLETTER_FORMAT_PLAIN'); ?>
						</option>
					</select>
				</div>
			</div>
			<div class="clr"></div>

			<div class="input-wrap">
				<label for="newsletter-template"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATE'); ?>:</label>
				<select name="newsletter[template]" id="newsletter-template">
					<option value=""><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_DEFAULT'); ?></option>
					<option value="-1" <?php if ($this->newsletter->template == '-1') : ?>selected="selected"<?php endif; ?>>
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_TEMPLATE_NONE'); ?>
					</option>
					<?php foreach ($this->templates as $t) : ?>
						<?php echo $sel = ($t->id == $this->newsletter->template) ? 'selected="selected"' : '' ; ?>
						<option <?php echo $sel; ?> value="<?php echo $t->id; ?>">
							<?php echo $t->name; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SHOW_HINT'); ?>">
				<label for="newsletter-published"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SHOW'); ?>:</label>
				<select name="newsletter[published]" id="newsletter-published">
					<option value="1" <?php if ($this->newsletter->published == '1') : ?>selected="selected"<?php endif; ?>>
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SHOW_SHOW'); ?>
					</option>
					<option value="0" <?php if ($this->newsletter->published == '0') : ?>selected="selected"<?php endif; ?>>
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SHOW_DONT_SHOW'); ?>
					</option>
				</select>
				<span class="hint">
					<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SHOW_HINT'); ?>
				</span>
			</div>

			<?php
				$link = $this->config->get('email_tracking_link', 'http://kb.mailchimp.com/article/how-open-tracking-works');
				$hint = JText::sprintf('COM_NEWSLETTER_NEWSLETTER_WHAT_IS_TRACKING', $link);
			?>
			<div class="input-wrap" data-hint="<?php echo $this->escape(strip_tags($hint)); ?>">
				<label for="newsletter-tracking"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_EMAIL_TRACKING'); ?>:</label>
				<select name="newsletter[tracking]" id="newsletter-tracking">
					<option value="1" <?php if ($this->newsletter->tracking) : ?>selected="selected"<?php endif; ?>>
						<?php echo JText::_('JYES'); ?>
					</option>
					<option value="0" <?php if (!$this->newsletter->tracking) : ?>selected="selected"<?php endif; ?>>
						<?php echo JText::_('JNO'); ?>
					</option>
				</select>
				<span class="hint">
					<?php echo $hint; ?>
				</span>
			</div>
		</fieldset>
	</div>

	<div class="col width-50 fltrt">
		<?php if ($this->newsletter->id) : ?>
			<table class="meta">
				<tbody>
					<?php if ($this->newsletter->id) : ?>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_ID'); ?>:</th>
							<td>
								<?php echo $this->newsletter->id; ?>
								<input type="hidden" name="newsletter[id]" value="<?php echo $this->newsletter->id; ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ($this->newsletter->created) : ?>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CREATED_DATE'); ?>:</th>
							<td>
								<?php echo JHTML::_('date', $this->newsletter->created, 'F d, Y @ g:ia'); ?>
								<input type="hidden" name="newsletter[created]" value="<?php echo $this->newsletter->created; ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ($this->newsletter->created_by) : ?>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CREATED_BY'); ?>:</th>
							<td>
								<?php
									$user = JUser::getInstance( $this->newsletter->created_by );
									echo (is_object($user) && $user->get('name') != '') ? $user->get('name') : 'Admin';
								?>
								<input type="hidden" name="newsletter[created_by]" value="<?php echo $this->newsletter->created_by; ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ($this->newsletter->modified) : ?>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED'); ?>:</th>
							<td>
								<?php echo JHTML::_('date', $this->newsletter->modified, 'F d, Y @ g:ia'); ?>
								<input type="hidden" name="newsletter[modified]" value="<?php echo $this->newsletter->modified; ?>" />
							</td>
						</tr>
					<?php endif; ?>

					<?php if ($this->newsletter->modified_by) : ?>
						<tr>
							<th><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_LAST_MODIFIED_BY'); ?>:</th>
							<td>
								<?php
									$user = JUser::getInstance( $this->newsletter->modified_by );
									echo (is_object($user) && $user->get('name') != '') ? $user->get('name') : 'Admin';
								?>
								<input type="hidden" name="newsletter[modified_by]" value="<?php echo $this->newsletter->modified_by; ?>" />
							</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>

			<?php
				$params = new JParameter( $this->newsletter->params );
			?>
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_DETAILS'); ?></legend>

				<div class="input-wrap">
					<label for="param-from_name"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_NAME'); ?>:</label>
					<input type="text" name="newsletter[params][from_name]" id="param-from_name" value="<?php echo $this->escape($params->get('from_name')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="param-from_address"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_FROM_EMAIL'); ?>:</label>
					<input type="text" name="newsletter[params][from_address]" id="param-from_address" value="<?php echo $this->escape($params->get('from_address')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="param-replyto_name"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_NAME'); ?>:</label>
					<input type="text" name="newsletter[params][replyto_name]" id="param-replyto_name" value="<?php echo $this->escape($params->get('replyto_name')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="param-replyto_address"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_MAILING_REPLYTO_EMAIL'); ?>:</label>
					<input type="text" name="newsletter[params][replyto_address]" id="param-replyto_address" value="<?php echo $this->escape($params->get('replyto_address')); ?>" />
				</div>
			</fieldset>
		<?php else : ?>
			<p class="info">
				<?php echo JText::_('COM_NEWSLETTER_MUST_SAVE_TO_ADD_CONTENT'); ?>
			</p>
		<?php endif; ?>
	</div>

	<br class="clear" />
	<hr />

	<div class="col width-100">
		<?php if ($this->newsletter->id != null) : ?>
			<?php if ($this->newsletter->template == '-1' || (!$this->newsletter->template && $this->newsletter->content != '')) : ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT'); ?></legend>

					<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML_HINT'); ?>">
						<label for="newsletter-html_content"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML'); ?></label>
						<textarea name="newsletter[html_content]" id="newsletter-html_content" cols="100" rows="20"><?php echo $this->escape( $this->newsletter->html_content ); ?></textarea>
						<span class="hint"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_HTML_HINT'); ?></span>
					</div>

					<div class="input-wrap" data-hint="<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN_HINT'); ?>">
						<label for="newsletter-plain_content"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN'); ?></label>
						<textarea name="newsletter[plain_content]" id="newsletter-plain_content" cols="100" rows="20"><?php echo $this->escape( $this->newsletter->plain_content ); ?></textarea>
						<span class="hint"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_CONTENT_PLAIN_HINT'); ?></span>
					</div>
				</fieldset>
			<?php else : ?>
				<a name="primary-stories"></a>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES'); ?>
						<a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=add&type=primary'); ?>">
							<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_PRIMARY_STORIES_ADD'); ?>
						</a>
					</legend>
					<?php //echo $tabs->startPane("content-pane"); ?>
					<?php echo JHtml::_('sliders.start', 'content-pane'); ?>
						<?php for ($i=0,$n=count($primary); $i<$n; $i++) : ?>
							<?php //echo $tabs->startPanel(($i+1) . ". " . $primary[$i]->title, "pstory-".($i+1)."") ; ?>
							<?php echo JHtml::_('sliders.panel', ($i+1) . ". " . $primary[$i]->title, "pstory-" . ($i+1)); ?>
								<table class="admintable">
									<tbody>
										<tr>
											<td colspan="2">
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=edit&type=primary&sid='.$primary[$i]->id); ?>">
													<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_EDIT_STORY'); ?>
												</a> |
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=delete&type=primary&sid='.$primary[$i]->id); ?>">
													<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_DELETE_STORY'); ?>
												</a>
											</td>
										</tr>
										<tr>
											<td class="key" width='20%'><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE'); ?>:</td>
											<td><?php echo $primary[$i]->title; ?></td>
										</tr>
										<tr>
											<td class="key" width='20%'><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER'); ?>:</td>
											<td>
												<input type="text" readonly="readonly" value="<?php echo $primary[$i]->order; ?>" style="width:30px;text-align:center;" />

												<?php if ($primary[$i]->order > 1) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=up&type=primary&sid='.$primary[$i]->id); ?>">
														<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_UP'); ?>
													</a>
												<?php endif ?>
												<?php if ($primary[$i]->order < $this->newsletter_primary_highest_order) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=down&type=primary&sid='.$primary[$i]->id); ?>">
														<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_DOWN'); ?>
													</a>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_STORY'); ?>:</td>
											<td><?php echo nl2br(stripslashes($primary[$i]->story)); ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE'); ?>:</td>
											<td><strong><?php echo $primary[$i]->readmore_title; ?></strong> - <?php echo $primary[$i]->readmore_link; ?></td>
										</tr>
									</tbody>
								</table>
							<?php //echo $tabs->endPanel(); ?>
						<?php endfor; ?>
					<?php //echo $tabs->endPane(); ?>
					<?php echo JHtml::_('sliders.end'); ?>
				</fieldset>
				<hr />
				<a name="secondary-stories"></a>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES'); ?>
						<a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=add&type=secondary'); ?>">
							<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_SECONDARY_STORIES_ADD'); ?>
						</a>
					</legend>
					<?php //echo $tabs->startPane("content-pane2"); ?>
					<?php echo JHtml::_('sliders.start', 'content-pane2'); ?>
						<?php for ($i=0,$n=count($secondary); $i<$n; $i++) : ?>
							<?php //echo $tabs->startPanel(($i+1) . ". " . $secondary[$i]->title, "sstory-".($i+1)."") ; ?>
							<?php echo JHtml::_('sliders.panel', ($i+1) . ". " . $secondary[$i]->title, "sstory-" . ($i+1)); ?>
								<table class="admintable">
									<tbody>
										<tr>
											<td colspan="2">
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=edit&type=secondary&sid='.$secondary[$i]->id); ?>">
													<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_EDIT_STORY'); ?>
												</a> |
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=delete&type=secondary&sid='.$secondary[$i]->id); ?>">
													<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_DELETE_STORY'); ?>
												</a>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_TITLE'); ?>:</td>
											<td><?php echo $secondary[$i]->title; ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_ORDER'); ?>:</td>
											<td>
												<input type="text" readonly="readonly" value="<?php echo $secondary[$i]->order; ?>" style="width:30px;text-align:center;" />
												<?php if ($secondary[$i]->order > 1) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=up&type=secondary&sid='.$secondary[$i]->id); ?>">
														<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_UP'); ?>
													</a>
												<?php endif; ?>

												<?php if ($secondary[$i]->order < $this->newsletter_secondary_highest_order) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=down&type=secondary&sid='.$secondary[$i]->id); ?>">
														<?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_MOVE_DOWN'); ?>
													</a>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_STORY'); ?>:</td>
											<td><?php echo nl2br(stripslashes($secondary[$i]->story)); ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('COM_NEWSLETTER_NEWSLETTER_STORY_READMORE'); ?>:</td>
											<td><strong><?php echo $secondary[$i]->readmore_title; ?></strong> - <?php echo $secondary[$i]->readmore_link; ?></td>
										</tr>
									</tbody>
								</table>
							<?php //echo $tabs->endPanel(); ?>
						<?php endfor; ?>
					<?php //echo $tabs->endPane(); ?>
					<?php echo JHtml::_('sliders.end'); ?>
				</fieldset>
			<?php endif; ?>
		<?php endif; ?>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
</form>