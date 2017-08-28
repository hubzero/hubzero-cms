<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
	->js()
	->css('jquery.fancybox.css', 'system')
	->css('edit')
	->js('setup');

$privacy = !$this->model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

// Get layout from project params or component
$layout = $this->model->params->get('layout', $this->config->get('layout', 'standard'));
$theme  = $this->model->params->get('theme', $this->config->get('theme', 'light'));

if ($layout == 'extended')
{
	// Include extended CSS
	$this->css('extended.css');

	// Include theme CSS
	$this->css('theme' . $theme . '.css');
}
else
{
	$this->css('standard.css');
}

?>
<div id="project-wrap" class="edit-project">
	<?php if ($layout == 'extended') {
		// Draw top header
		$this->view('_topheader', 'projects')
		     ->set('model', $this->model)
		     ->set('publicView', false)
		     ->set('option', $this->option)
		     ->display();
		// Draw top menu
		$this->view('_topmenu', 'projects')
		     ->set('model', $this->model)
		     ->set('active', 'edit')
		     ->set('tabs', array())
		     ->set('option', $this->option)
		     ->set('guest', false)
		     ->set('publicView', false)
		     ->display();
	?>
		<div class="project-inner-wrap">
	<?php
	} else {
		$this->view('_header', 'projects')
		     ->set('model', $this->model)
		     ->set('showPic', 1)
		     ->set('showPrivacy', 0)
		     ->set('goBack', 1)
		     ->set('showUnderline', 1)
		     ->set('option', $this->option)
		     ->display(); ?>
	<?php } ?>
	<?php
		// Display status message
		$this->view('_statusmsg', 'projects')
		     ->set('error', $this->getError())
		     ->set('msg', $this->msg)
		     ->display();
	?>

	<div id="edit-project-content">
		<h3 class="edit-title"><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_PROJECT')); ?></h3>
		<section class="main section">
			<div class="grid">
				<div class="col span3">
				<?php
					// Display sections menu
					$this->view('_sections')
					     ->set('sections', $this->sections)
					     ->set('section', $this->section)
					     ->set('option', $this->option)
					     ->set('model', $this->model)
					     ->display();
				?>

				<?php if ($this->section != 'info') { ?>
				<div class="tips">
					<h3><?php echo Lang::txt('COM_PROJECTS_TIPS'); ?></h3>
				<?php if ($this->section == 'team') { ?>
						<h4><?php echo Lang::txt('PLG_PROJECTS_TEAM_HOWTO_ROLES_TIPS'); ?></h4>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_OWNERS')); ?> </span><?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_ONE'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_TWO'); ?></li>
							<li><strong><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_MANAGER_CAN_THREE'); ?></strong></li>
						</ul>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_COLLABORATORS')); ?> </span><?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_ONE'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_TWO'); ?></li>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_COLLABORATOR_CAN_THREE'); ?></li>
						</ul>
						<p><span class="italic prominent"><?php echo ucfirst(Lang::txt('COM_PROJECTS_LABEL_REVIEWER')); ?></span> <?php echo Lang::txt('COM_PROJECTS_CAN'); ?>:</p>
						<ul>
							<li><?php echo Lang::txt('COM_PROJECTS_HOWTO_ROLES_REVIEWER_CAN_ONE'); ?></li>
						</ul>
				<?php }
				 else if ($this->section == 'settings') { ?>
						<h4><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE'); ?></h4>
						<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_PUBLIC_PAGE_EXPLAIN'); ?></p>
					<?php if ($this->config->get('grantinfo', 0)) { ?>
						<h5><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_WHY'); ?></h5>
						<p><?php echo Lang::txt('COM_PROJECTS_HOWTO_GRANTINFO_BECAUSE'); ?></p>
					<?php } ?>
				<?php } ?>
				</div>
				<?php } ?>
			</div><!-- / .aside -->
			<div id="edit-project" class="col span9 omega">
				<form id="hubForm" class="full" method="post" action="<?php echo Route::url($this->model->link() . '&task=save'); ?>">
					<div>
						<input type="hidden" id="pid" name="id" value="<?php echo $this->model->get('id'); ?>" />
						<input type="hidden"  name="task" value="save" />
						<input type="hidden"  name="active" value="<?php echo $this->section; ?>" />
						<input type="hidden"  name="name" value="<?php echo $this->model->get('alias'); ?>" />
						<?php echo Html::input('token'); ?>
						<?php echo Html::input('honeypot'); ?>
						<?php
							switch ($this->section)
							{
								case 'info':
								default:
						?>
						<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO')); ?></h4>
							<div>
								<table id="infotbl">
									<tbody>
										<tr>
											<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?></td>
											<td><?php echo $this->model->get('alias'); ?></td>
										</tr>
										<tr>
											<td class="htd"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></td>
											<td><input name="title" maxlength="250" type="text" value="<?php echo $this->escape($this->model->get('title')); ?>" class="long" /></td>
										</tr>
										<tr>
											<td class="htd"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></td>
											<td>
												<span class="clear"></span>
												<?php
													echo $this->editor('about', $this->escape($this->model->about('raw')), 35, 25, 'about', array('class' => 'minimal no-footer'));
												?>
											</td>
										</tr>
									</tbody>
								</table>
								<?php
									// Display project image upload
									$this->view('_picture')
									     ->set('model', $this->model)
									     ->set('option', $this->option)
									     ->display();
								?>
							</div><!-- / .basic info -->
							<p class="submitarea">
								<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
								<span><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=info'); ?>" class="btn btn-cancel"><?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?></a></span>
							</p>
						<?php
								break;
								case 'team':
						?>
						<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_TEAM')); ?></h4>
						<div id="cbody">
							<?php echo $this->content; ?>
						</div>
						<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_PROJECT') . ' ' . Lang::txt('COM_PROJECTS_OWNER'); ?> <?php if ($this->model->access('manager')) { ?>
							<span class="mini"><a href="<?php echo Route::url($this->model->link('team') . '&action=changeowner'); ?>" class="showinbox"><?php echo ucfirst(Lang::txt('COM_PROJECTS_EDIT')); ?></a></span>
						<?php } ?></h5>
						<?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn'))
						{
							$ownedby = ucfirst(Lang::txt('COM_PROJECTS_GROUP')) . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">' . ' ' . $this->model->groupOwner('description') . ' (' . $cn . ')</a>';
						}
						else
						{
							$ownedby = '<a href="' . Route::url('index.php?option=com_members&id=' . $this->model->owner('id')) . '">' . $this->model->owner('name') . '</a>';
						} echo '<span class="mini">' . $ownedby . '</span>'; ?>
						<?php
								break;
								case 'settings':
						?>
						<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_SETTINGS')); ?></h4>
						<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></h5>
						<label><input class="option" name="private" type="radio" value="1" <?php if (!$this->model->isPublic()) { echo 'checked="checked"'; }?> /> <?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PRIVATE'); ?></label>
						<label><input class="option" name="private" type="radio" value="0" <?php if ($this->model->isPublic()) { echo 'checked="checked"'; }?> /> <?php echo Lang::txt('COM_PROJECTS_PRIVACY_EDIT_PUBLIC'); ?></label>
						<?php if ($this->model->isPublic()) { ?>
						<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_OPTIONS_FOR_PUBLIC'); ?></h5>
						<p class="hint"><?php echo Lang::txt('COM_PROJECTS_YOUR_PROJECT_IS'); ?> <span class="prominent urgency"><?php echo $privacy; ?></span></p>
						<label>
							<input type="hidden"  name="params[team_public]" value="0" />
							<input type="checkbox" class="option" name="params[team_public]" value="1" <?php if ($this->model->params->get( 'team_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_TEAM_PUBLIC'); ?>
						</label>

						<?php if ($this->publishing) { ?>
						<label>
							<input type="hidden"  name="params[publications_public]" value="0" />
							<input type="checkbox" class="option" name="params[publications_public]" value="1" <?php if ($this->model->params->get( 'publications_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS_PUBLIC'); ?>
						</label>
						<?php } ?>

						<?php
						$pparams = Plugin::params( 'projects', 'notes' );
						if ($pparams->get('enable_publinks')) { ?>
						<label>
							<input type="hidden"  name="params[notes_public]" value="0" />
							<input type="checkbox" class="option" name="params[notes_public]" value="1" <?php if ($this->model->params->get( 'notes_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_NOTES_PUBLIC'); ?>
						</label>
						<?php } ?>

						<?php
						$pparams = Plugin::params( 'projects', 'files' );
						if ($pparams->get('enable_publinks')) { ?>
						<label>
							<input type="hidden"  name="params[files_public]" value="0" />
							<input type="checkbox" class="option" name="params[files_public]" value="1" <?php if ($this->model->params->get( 'files_public')) { echo ' checked="checked"'; } ?> /> <?php echo Lang::txt('COM_PROJECTS_FILES_PUBLIC'); ?>
						</label>
						<?php } ?>

						<?php } ?>
						<?php if ($this->config->get('grantinfo', 0)) { ?>
						<h5 class="terms-question"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO'); ?></h5>
						<?php
							$approved = ($this->model->params->get( 'grant_status') == 1) ? 1 : 0;
							if ($approved)
							{ ?>
							<p class="notice notice_passed"><?php echo Lang::txt('COM_PROJECTS_GRANT_APPROVED_WITH_CODE'); ?> <span class="prominent"><?php echo htmlentities(html_entity_decode($this->model->params->get( 'grant_approval', 'N/A'))); ?></span></p>
						<?php } else { ?>
							<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_INFO_WHY'); ?></p>
						<?php } ?>
						<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:
						<?php if ($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->model->params->get( 'grant_title', 'N/A'))) . '</span>'; } else {  ?>
						 <input name="params[grant_title]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get( 'grant_title'))); ?>" class="long" />
						<?php } ?>
						</label>
						<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:
						<?php if ($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->model->params->get( 'grant_PI', 'N/A'))) . '</span>'; } else {  ?>
						 <input name="params[grant_PI]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get( 'grant_PI'))); ?>" class="long"  />
						<?php } ?>
						</label>
						<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:
						<?php if ($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->model->params->get( 'grant_agency', 'N/A'))) . '</span>'; } else {  ?>
						 <input name="params[grant_agency]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get( 'grant_agency'))); ?>" class="long"  />
						<?php } ?>
						</label>
						<label class="terms-label"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:
						<?php if ($approved) { echo '<span class="prominent">' . htmlentities(html_entity_decode($this->model->params->get( 'grant_budget', 'N/A'))) . '</span>'; } else {  ?>
						 <input name="params[grant_budget]" maxlength="250" type="text" value="<?php echo htmlentities(html_entity_decode($this->model->params->get( 'grant_budget'))); ?>" class="long"  />
						<?php } ?>
						</label>
						<?php if (!$approved) { ?>
							<label><input class="option" name="params[grant_status]" type="checkbox" value="0" <?php if ($this->model->params->get( 'grant_status') == 2) { echo 'checked="checked"'; } ?> /> <?php echo $this->model->params->get( 'grant_status') == 2
							? Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_RESUBMIT_FOR_APPROVAL')
							: Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_NOTIFY_ADMIN') ; ?></label>
						<?php } ?>
						<?php } ?>
						<p class="submitarea">
							<input type="submit" class="btn" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>" class="btn btn-cancel"><?php echo Lang::txt('COM_PROJECTS_CANCEL'); ?></a>
						</p>
						<?php
							break;
							case 'info_custom': ?>
								<fieldset>
									<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO')); ?></legend>

									<label for="field-name">
										<?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?>
										<input type="text" name="name" id="field-name" disabled="disabled" readonly="readonly" class="disabled readonly" value="<?php echo $this->model->get('alias'); ?>" />
									</label>

									<label for="field-title">
										<?php echo Lang::txt('COM_PROJECTS_TITLE'); ?>
										<input name="title" id="field-title" maxlength="250" type="text" value="<?php echo $this->escape($this->model->get('title')); ?>" class="long" />
									</label>

									<label for="field-title">
										<?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></td>
										<?php echo $this->editor('about', $this->escape($this->model->about('raw')), 35, 25, 'about', array('class' => 'minimal no-footer')); ?>
									</label>
								</fieldset>

								<fieldset>
									<legend><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_INFO_EXTENDED')); ?></legend>
									<?php
									// Convert to XML so we can use the Form processor
									$xml = Components\Projects\Models\Orm\Description\Field::toXml($this->fields, 'edit');
									// Create a new form
									Hubzero\Form\Form::addFieldPath(Component::path('com_projects') . DS . 'models' . DS . 'orm' . DS . 'description' . DS. 'fields');

									$form = new Hubzero\Form\Form('description', array('control' => 'description'));
									$form->load($xml);

									$data = new stdClass;
									$data->textbox = 'abd';
									$data->projecttags = 'testing, tagging';

									$form->bind($this->data);

									foreach ($form->getFieldsets() as $fieldset)
									{
										foreach ($form->getFieldset($fieldset->name) as $field)
										{
											echo $field->label;
											echo $field->input;
											echo $field->description;
										}
									}
									?>
								</fieldset>
								<fieldset>
									<?php
									// Display project image upload
									$this->view('_picture')
									     ->set('model', $this->model)
									     ->set('option', $this->option)
									     ->display();
									?>
								</fieldset>
								<input type="submit" class="btn btn-success" value="<?php echo Lang::txt('COM_PROJECTS_SAVE_CHANGES'); ?>"  />
								<?php
							break;
							}
						?>
					</div>
				</form>
				</div><!-- / .omega -->
			</div><!-- / .grid -->
		</section><!-- / .main section -->
	</div><!-- / #edit-project-content -->
<?php if ($layout == 'extended') { ?>
</div><!-- / .project-inner-wrap -->
<?php } ?>
</div>
<?php if ($this->section == 'info') { ?>
	<div id="cancel-project">
		<p class="right_align"><?php echo Lang::txt('Need to cancel project? You have an option to permanently '); ?> <a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=delete'); ?>" id="delproject"><?php echo strtolower(Lang::txt('delete')); ?></a> <?php echo Lang::txt('your project.'); ?></p>
	</div>
<?php } ?>
