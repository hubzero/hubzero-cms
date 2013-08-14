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

//set title
$text = ($this->task == 'edit' ? JText::_('Edit') : JText::_('New'));
JToolBarHelper::title(JText::_('Newsletter') . ': <small><small>[ ' . $text . ' ]</small></small>', 'newsletter.png');

//add buttons to toolbar
JToolBarHelper::save();
if ($this->newsletter->id)
{
	JToolBarHelper::apply();
}
JToolBarHelper::cancel();

//primary and secondary stories
$primary = $this->newsletter_primary;
$secondary = $this->newsletter_secondary; 

// Instantiate the sliders object
jimport('joomla.html.pane');
$tabs =& JPane::getInstance('sliders');

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();
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

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('Newsletter Details'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<th><?php echo JText::_('Name:'); ?></th>
						<td>
							<input type="text" name="newsletter[name]" value="<?php echo $this->newsletter->name; ?>" />
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('Alias:'); ?></th>
						<td>
							<input type="text" name="newsletter[alias]" value="<?php echo $this->newsletter->alias; ?>" />
							<span class="hint"><?php echo JText::_('Appears in the URL (ex. january2013update)'); ?></span>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('Issue:'); ?></th>
						<td>
							<input type="text" name="newsletter[issue]" value="<?php echo $this->newsletter->issue; ?>" />
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Email Format:'); ?></th>
						<td>
							<select name="newsletter[type]">
								<option value="html" <?php if($this->newsletter->type == 'html') : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('HTML'); ?>
								</option>
								<option value="plain" <?php if($this->newsletter->type == 'plain') : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('Plain Text'); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<th><?php echo JText::_('Template:'); ?></th>
						<td>
							<select name="newsletter[template]">
								<option value=""><?php echo JText::_('- Select a Template &mdash;'); ?></option>
								<option value="-1" <?php if($this->newsletter->template == '-1') : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('No Template (Newsletter Content Includes HTML Template if HTML Email Type)'); ?>
								</option>
								<?php foreach($this->templates as $t) : ?>
									<?php echo $sel = ($t->id == $this->newsletter->template) ? 'selected="selected"' : '' ; ?>
									<option <?php echo $sel; ?> value="<?php echo $t->id; ?>">
										<?php echo $t->name; ?>
									</option>
								<?php endforeach; ?> 
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th width="150"><?php echo JText::_('Show Newsletter on HUB:'); ?></th>
						<td>
							<select name="newsletter[published]">
								<option value="1" <?php if($this->newsletter->published == '1') : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('Show'); ?>
								</option>
								<option value="0" <?php if($this->newsletter->published == '0') : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('Don\'t Show'); ?>
								</option>
							</select>
							<span class="hint">
								<?php echo JText::_('You may want to hide private email newsletters or plain text email newsletters from showing on the HUB.'); ?>
							</span>
						</td>
					</tr>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr>
						<th><?php echo JText::_('Email Tracking:'); ?></th>
						<td>
							<select name="newsletter[tracking]">
								<option value="1" <?php if ($this->newsletter->tracking) : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('Yes'); ?>
								</option>
								<option value="0" <?php if (!$this->newsletter->tracking) : ?>selected="selected"<?php endif; ?>>
									<?php echo JText::_('No'); ?>
								</option>
							</select>
							<span class="hint">
								<?php echo JText::_('What is email tracking?'); ?> 
								<a target="_blank" href="<?php echo $this->config->get('email_tracking_link', 'http://kb.mailchimp.com/article/how-open-tracking-works'); ?>">
									<?php echo JText::_('Click here'); ?>
								</a>
								<?php echo JText::_(' to learn more about we can track open rates and click throughs.'); ?>
							</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div class="col width-50 fltrt">
		<?php if ($this->newsletter->id) : ?>
			<table class="meta">
				<tbody>
					<?php if ($this->newsletter->id) : ?>
						<tr>
							<th><?php echo JText::_('Newsletter ID:'); ?></th>
							<td>
								<?php echo $this->newsletter->id; ?>
								<input type="hidden" name="newsletter[id]" value="<?php echo $this->newsletter->id; ?>" />
							</td>
						</tr>
					<?php endif; ?>
				
					<?php if ($this->newsletter->created) : ?>
						<tr>
							<th><?php echo JText::_('Created Date:'); ?></th>
							<td>
								<?php echo date("F d, Y @ g:ia", strtotime($this->newsletter->created)); ?>
								<input type="hidden" name="newsletter[created]" value="<?php echo $this->newsletter->created; ?>" />
							</td>
						</tr>
					<?php endif; ?>
				
					<?php if ($this->newsletter->created_by) : ?>
						<tr>
							<th><?php echo JText::_('Created By:'); ?></th>
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
							<th><?php echo JText::_('Last Modified On:'); ?></th>
							<td>
								<?php echo date("F d, Y @ g:ia", strtotime($this->newsletter->modified)); ?>
								<input type="hidden" name="newsletter[modified]" value="<?php echo $this->newsletter->modified; ?>" />
							</td>
						</tr>
					<?php endif; ?>
				
					<?php if ($this->newsletter->modified_by) : ?>
						<tr>
							<th><?php echo JText::_('Last Modified By:'); ?></th>
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
				<legend><?php echo JText::_('Newsletter Mailing Details'); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<th width="100px"><?php echo JText::_('From Name:'); ?></th>
							<td>
								<input type="text" name="newsletter[params][from_name]" value="<?php echo $params->get('from_name'); ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('From Email:'); ?></th>
							<td>
								<input type="text" name="newsletter[params][from_address]" value="<?php echo $params->get('from_address'); ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('Reply-To Name:'); ?></th>
							<td>
								<input type="text" name="newsletter[params][replyto_name]" value="<?php echo $params->get('replyto_name'); ?>" />
							</td>
						</tr>
						<tr>
							<th><?php echo JText::_('Reply-To Email:'); ?></th>
							<td>
								<input type="text" name="newsletter[params][replyto_address]" value="<?php echo $params->get('replyto_address'); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php else : ?>
			<p class="info">
				<?php echo JText::_('You must create the newsletter with basic details before adding newsletter stories or content.'); ?>
			</p>
		<?php endif; ?>
	</div>
	
	<br class="clear" /> 
	<hr />
	
	<div class="col width-100">
		<?php if($this->newsletter->id != null) : ?>
			<?php if($this->newsletter->template == '-1' || (!$this->newsletter->template && $this->newsletter->content != '')) : ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('Newsletter Content'); ?></legend>
					<table class="admintable">
						<tbody>
							<tr>
								<td>
									<strong><?php echo JText::_('Content:'); ?></strong> (<span class="hint"><?php echo JText::_('You may use HTML tags if Email Type is \'HTML\' above.'); ?></span>)
									<textarea name="newsletter[content]" id="" cols="100" rows="20"><?php echo $this->escape( $this->newsletter->content ); ?></textarea>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			<?php else : ?>
				<a name="primary-stories"></a>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('Newsletter Primary Stories'); ?>  
						<a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=add&type=primary'); ?>">
							<?php echo JText::_('Add Primary Story'); ?>
						</a>
					</legend>
					<?php echo $tabs->startPane("content-pane"); ?>
						<?php for($i=0,$n=count($primary); $i<$n; $i++) : ?>
							<?php echo $tabs->startPanel(($i+1) . ". " . $primary[$i]->title, "pstory-".($i+1)."") ; ?>
								<table class="admintable">
									<tbody>
										<tr>
											<td colspan="2">
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=edit&type=primary&sid='.$primary[$i]->id); ?>">
													<?php echo JText::_('Edit Story'); ?>
												</a> | 
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=delete&type=primary&sid='.$primary[$i]->id); ?>">
													<?php echo JText::_('Delete Story'); ?>
												</a>
											</td>
										</tr>
										<tr>
											<td class="key" width='20%'><?php echo JText::_('Title:'); ?></td>
											<td><?php echo $primary[$i]->title; ?></td>
										</tr>
										<tr>
											<td class="key" width='20%'><?php echo JText::_('Order:'); ?></td>
											<td>
												<input type="text" readonly="readonly" value="<?php echo $primary[$i]->order; ?>" style="width:30px;text-align:center;" /> 
												
												<?php if ($primary[$i]->order > 1) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=up&type=primary&sid='.$primary[$i]->id); ?>">
														<?php echo JText::_('Move Up &uarr;'); ?>
													</a>
												<?php endif ?>
												<?php if ($primary[$i]->order < $this->newsletter_primary_highest_order) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=down&type=primary&sid='.$primary[$i]->id); ?>">
														<?php echo JText::_('Move Down &darr;'); ?>
													</a>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Story:'); ?></td>
											<td><?php echo nl2br(stripslashes($primary[$i]->story)); ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Read More Link:'); ?></td>
											<td><strong><?php echo $primary[$i]->readmore_title; ?></strong> - <?php echo $primary[$i]->readmore_link; ?></td>
										</tr>
									</tbody>
								</table>
							<?php echo $tabs->endPanel(); ?>
						<?php endfor; ?>
					<?php echo $tabs->endPane(); ?>
				</fieldset>
				<hr />
				<a name="secondary-stories"></a>
				<fieldset class="adminform">
					<legend>
						<?php echo JText::_('Newsletter Secondary Stories'); ?>
						<a class="fltrt" style="padding-right:15px" href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=add&type=secondary'); ?>">
							<?php echo JText::_('Add Secondary Story'); ?>
						</a>
					</legend>
					<?php echo $tabs->startPane("content-pane2"); ?>
						<?php for($i=0,$n=count($secondary); $i<$n; $i++) : ?>
							<?php echo $tabs->startPanel(($i+1) . ". " . $secondary[$i]->title, "sstory-".($i+1)."") ; ?>
								<table class="admintable">
									<tbody>
										<tr>
											<td colspan="2">
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=edit&type=secondary&sid='.$secondary[$i]->id); ?>">
													<?php echo JText::_('Edit Story'); ?>
												</a> | 
												<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=delete&type=secondary&sid='.$secondary[$i]->id); ?>">
													<?php echo JText::_('Delete Story'); ?>
												</a>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Title:'); ?></td>
											<td><?php echo $secondary[$i]->title; ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Order:'); ?></td>
											<td>
												<input type="text" readonly="readonly" value="<?php echo $secondary[$i]->order; ?>" style="width:30px;text-align:center;" /> 
												<?php if ($secondary[$i]->order > 1) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=up&type=secondary&sid='.$secondary[$i]->id); ?>">
														<?php echo JText::_('Move Up &uarr;'); ?>
													</a>
												<?php endif; ?>
												
												<?php if ($secondary[$i]->order < $this->newsletter_secondary_highest_order) : ?>
													<a href="<?php echo JRoute::_('index.php?option=com_newsletter&controller=story&id='.$this->newsletter->id.'&task=reorder&direction=down&type=secondary&sid='.$secondary[$i]->id); ?>">
														<?php echo JText::_('Move Down &darr;'); ?>
													</a>
												<?php endif; ?>
											</td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Story:'); ?></td>
											<td><?php echo nl2br(stripslashes($secondary[$i]->story)); ?></td>
										</tr>
										<tr>
											<td class="key"><?php echo JText::_('Read More Link:'); ?></td>
											<td><strong><?php echo $primary[$i]->readmore_title; ?></strong> - <?php echo $primary[$i]->readmore_link; ?></td>
										</tr>
									</tbody>
								</table>
							<?php echo $tabs->endPanel(); ?>
						<?php endfor; ?>
					<?php echo $tabs->endPane(); ?>
				</fieldset>
			<?php endif; ?>
		<?php endif; ?>
	</div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
</form>