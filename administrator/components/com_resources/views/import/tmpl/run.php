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

// set title
JToolBarHelper::title(JText::_('COM_RESOURCES_IMPORT_TITLE_RUN'), 'script.png');

// add import styles and scripts
$this->js('import');
$this->css('import');
?>
<script src="/media/system/js/handlebars.js"></script>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="index.php?option=com_resources&amp;controller=import&amp;task=dorun" method="post" name="adminForm" id="adminForm">
	
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td>
						<?php if ($this->dryRun) : ?>
							<div class="dryrun-message">
								<strong><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_NOTICE'); ?></strong>
								<p><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_NOTICE_DESC'); ?></p>
							</div>
						<?php endif; ?>

						<div class="countdown" data-timeout="5">
							<?php echo JText::sprintf('COM_RESOURCES_IMPORT_RUN_START', '<span>5</span>'); ?>
						</div>
						<div class="countdown-actions">
							<button type="button" class="start"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_BUTTON_START'); ?></button>
							<button type="button" class="stop"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_BUTTON_STOP'); ?></button>

							<button type="button" class="start-over"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_BUTTON_RERUN'); ?></button>
							<?php if ($this->dryRun) : ?>
								<button type="button" class="start-real"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_BUTTON_REAL'); ?></button>
							<?php endif; ?>
						</div>

						<hr />
						
						<strong><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_PROGRESS'); ?><span class="progress-percentage">0%</span></strong>
						<div class="progress"></div>

						<hr />

						<strong><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULTS'); ?><span class="results-stats"></span></strong>
						<div class="results">
							<span class="hint"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULTS_WAITING'); ?></span>
						</div>
						<script id="resource-template" type="text/x-handlebars-template">
							<h3 class="resource-title">
								{{#if record.errors}}<span class="has-errors"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_CONTAINSERRORS'); ?></span>{{/if}}
								{{#if record.notices}}<span class="has-notices"><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_CONTAINSNOTICES'); ?></span>{{/if}}
								{{{ record.resource.title }}}
							</h3>
							
							<div class="resource-data">
								<div class="grid">
									{{#if record.errors}}
										<div class="col width-100">
											<div class="errors">
												<strong><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_ERRORMESSAGE'); ?></strong>
												<ol>
													{{#each record.errors}}
														<li>{{this}}</li>
													{{/each}}
												</ol>
											</div>
										</div>
									{{/if}}

									{{#if record.notices}}
										<div class="col width-100">
											<div class="notices">
												<strong><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_NOTICEMESSAGE'); ?></strong>
												<ol>
													{{#each record.notices}}
														<li>{{{this}}}</li>
													{{/each}}
												</ol>
											</div>
										</div>
									{{/if}}

									<div class="col width-60 fltlft">
										{{{resource_data record}}}
									</div>
									<div class="col width-40 fltrt">
										<h4><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_CHILDREN'); ?></h4>
										{{{child_resource_data record.children}}}
										<hr />

										<h4><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_CONTRIBUTORS'); ?></h4>
										<table>
											{{#each record.contributors}}
												<tr>
													<td>
														<span class="contributor-name">{{{ name }}}</span>
														<span class="contributor-org">{{{ organization }}}</span>
													</td>
													<td>
														<span class="contributor-role">
															{{#if role}}
																{{{ucfirst role }}}
															{{else}}
																Author
															{{/if}}
														</span>
													</td>
												</tr>
											{{/each}}
										</table>

										<hr />

										<h4><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_TAGS'); ?></h4>
										<table>
											<tr>
												<td>
													{{#each record.tags}}
														{{{ this }}}<br />
													{{else}}
														<span class="hint">No Tags</span>
													{{/each}}
												</td>
											</tr>
										</table>

										<hr />

										<h4><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_CUSTOM'); ?></h4>
										<table>
											{{#each record.custom}}
												<tr>
													<th width="25%">{{{ ucfirst @key }}}</th>
													<td>{{{ this }}}</td>
												</tr>
											{{/each}}
										</table>

									</div>
									<br class="clr" />
									<hr />
									
									<div class="unused-data">
										<h4><?php echo JText::_('COM_RESOURCES_IMPORT_RUN_RESULT_UNUSED'); ?></h4>
										<pre>{{print_json_data raw._unused}}</pre>
									</div>
								</div>
							</div>
						</script>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="dorun" />
	<input type="hidden" name="id" value="<?php echo $this->import->get('id'); ?>" />
	<input type="hidden" name="dryrun" value="<?php echo $this->dryRun; ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>