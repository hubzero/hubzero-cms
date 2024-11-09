<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$selected = array();
if (count($this->authors) > 0)
{
	foreach ($this->authors as $sel)
	{
		$selected[] = $sel->project_owner_id;
	}
}

?>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/team/assets/js/selector.js"></script>
<div id="abox-content">
	<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_ADD_NEW'); ?></h3>
	<form id="add-author" class="add-author" method="post" action="<?php echo Route::url(Route::url($this->publication->link('edit'))); ?>">
		<fieldset>
			<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
			<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
			<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
			<input type="hidden" name="p" value="<?php echo $this->props; ?>" />
			<input type="hidden" name="active" value="publications" />
			<input type="hidden" name="action" value="additem" />
			<?php if ($this->model->isProvisioned()) { ?>
				<input type="hidden" name="id" value="<?php echo $this->publication->id; ?>" />
				<input type="hidden" name="task" value="submit" />
				<input type="hidden" name="option" value="com_publications" />
				<input type="hidden" name="ajax" value="0" />
			<?php } else { ?>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
			<?php } ?>
		</fieldset>
		<div id="quick-add" class="quick-add">
			<?php // if ($this->model->isProvisioned()) { ?>
				<div class="block">
					<label>
						<span class="formlabel"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_LOOK_UP_BY_ID')); ?>:</span>
						<?php
						if (count($this->mc) > 0)
						{
							echo $this->mc[0];
							?>
							<script>
								if ($('.autocomplete').length)
								{
									$('.autocomplete').each(function(i, input) {

										id = $(input).attr('id');
										if (id != 'uid')
										{
											return false;
										}

										$(input).on('change', function(e)
										{
											var uid = $(input).val();

											if (uid)
											{
												var item = $($('.token-input-token-acm')[0]);
												if (item.length)
												{
													var name = item.attr('data-name');
													var org  = item.attr('data-org');

													$('#organization').val(org);

													var parts = name.split(" ");

													// Complete name
													if (parts.length > 1 && !$('#firstName').val() && !$('#lastName').val())
													{
														$('#lastName').val(parts[parts.length - 1]);
														parts.pop();
														var first = parts.join(" ");
														$('#firstName').val(first);
													}
												}
											}
										});
									});
								}

							</script>
						<?php } else { ?>
							<input type="text" name="uid" id="uid" value="" size="35" />
						<?php } ?>
					</label>
				</div>
			<?php //} ?>
			<div class="block">
				<div class="block-liner">
					<label for="firstName">
						<span class="formlabel"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_FIRST_NAME')); ?>*:</span>
						<input type="text" name="firstName" id="firstName" class="long" value="" maxlength="255" />
					</label>
				</div>
				<div class="block-liner">
					<label for="lastName">
						<span class="formlabel"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_LAST_NAME')); ?>*:</span>
						<input type="text" name="lastName" id="lastName" class="long" value="" maxlength="255" />
					</label>
				</div>
			</div>
			<div class="block">
				<div class="block-liner">
					<label for="organization">
						<span class="formlabel"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_ORGANIZATION')); ?>*:</span>
						<input type="text" class="long" name="organization" id="organization" value="" maxlength="255" />
						<?php
                            // Add in class for JS selector to conditionally retrieve data from RoR Api
                            if (\Component::params('com_members')->get('rorApi')) {
                                echo "<div id='autocomplete-organization' class='rorApiAvailable'></div>";
                            }
                        ?>
						<p class="hint"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_HINT'); ?></p>
					</label>
				</div>
			</div>
			<div class="block">
				<div class="block-liner">
					<label for="orcid">
						<span class="formlabel"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_ORCID'); ?></span>
						<input type="text" class="long" name="orcid" id="orcid" placeholder="####-####-####-####" value="" maxlength="255" />
						<p id="orcid-message" class="hint"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_ORCID_DESC'); ?></p>
						<span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
					</label>
				</div>
			</div>
			<?php if (!$this->model->isProvisioned()) { ?>
				<div class="block">
					<p class="invite-question"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_INVITE_TO_TEAM')); ?></p>
					<div class="block-liner">
					<label for="email">
						<span class="formlabel"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_EMAIL')); ?>:</span>
						<input type="text" class="long" name="email" value="" maxlength="255" />
					</label>
					</div>
				</div>
			<?php } ?>

			<div class="submitarea">
				<div id="status-box"></div>
				<a class="btn btn-success active" id="b-add"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_SAVE_NEW'); ?></a>
			</div>
		</div>
	</form>
</div>