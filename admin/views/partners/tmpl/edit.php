<?php
// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Partners\Helpers\Permissions::getActions('partner');

// Toolbar is a helper class to simplify the creation of Toolbar 
// titles, buttons, spacers and dividers in the Admin Interface.
//
// Here we'll had the title of the component and options
// for saving based on if the user has permission to
// perform such actions. Everyone gets a cancel button.
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_PARTNERS') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('partner');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// do field validation, where we make sure required fields were not left blank
	if ($('#field-name').val() == '' || $('#field-date_joined').val() == ''|| $('#field-site_url').val() == '' || $('#field-logo_url').val() == '' 
		|| $('#field-activities').val() == '' || $('#field-about').val() == '' ){
		alert("<?php echo Lang::txt('COM_PARTNERS_ERROR_MISSING_FIELDS'); ?>");
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>
			<!--to access values from database, need to use $this->escape($this->row->get('variable name in database')) -->
			
			<div class="input-wrap">
				<label for="field-name"><?php echo Lang::txt('COM_PARTNERS_FIELD_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>	
				<input type="text" name="fields[name]" id="field-name" size="35" value="<?php echo $this->escape($this->row->get('name')); ?>" />
			</div>
			<!--Date joined-->
			<div class="input-wrap">
				<label for="field-date_joined"><?php echo Lang::txt('COM_PARTNERS_FIELD_DATE_JOINED'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[date_joined]" id="field-date_joined" size="35" value="<?php echo $this->escape($this->row->get('date_joined')); ?>" />
			</div>
			<!--URL to partners website-->
			<div class="input-wrap">
				<label for="field-site_url"><?php echo Lang::txt('COM_PARTNERS_FIELD_LINK'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[site_url]" id="field-site_url" size="35" value="<?php echo $this->escape($this->row->get('site_url')); ?>" />
			</div>
			<!--Logo link field -->
			<div class="input-wrap">
				<label for="field-logo_url"><?php echo Lang::txt('COM_PARTNERS_FIELD_LOGO'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[logo_url]" id="field-logo_url" size="35" value="<?php echo $this->escape($this->row->get('logo_url')); ?>" />
			</div>

			<!--QUBES group url (cidnumber)-->
			<div class="input-wrap	">
			<label for="field-groups_cn" name=fields[groups_cn] id="field-groups_cn"><?php echo Lang::txt('COM_PARTNERS_FIELD_GROUP_CN'); ?> </label>
			
			<select name="fields[groups_cn]" id="fields-groups_cn style="max-width: 15em;">
							<!--echo the select message if no group has been selected, else show the previously selected group -->
							<?php if ($this->row->get('groups_cn') == ''){ ?>
							<option value=""><?php echo Lang::txt('COM_PARTNERS_SELECT'); ?></option>
								<?php } else{ ?>
							<option value=""><?php echo $this->row->get('groups_cn'); ?></option>
								<?php } ?>

							<?php
							foreach ($this->grouprows as $grouprow)
							{
								echo '<option value="' . $grouprow->cn . '">'  . $grouprow->cn . '</option>' . "\n";
							}
							?>
						</select>
			</div>

			<!--Social media url-->
			<div class="input-wrap">
				<label for="field-social_media_url"><?php echo Lang::txt('COM_PARTNERS_FIELD_SOCIAL_MEDIA'); ?> </label>
				<input type="text"  name="fields[social_media_url]" id="field-social_media_url" size="35" value="<?php echo $this->escape($this->row->get('social_media_url')); ?>" />
			</div>
			<!--Qubes liason member number-->
			<div class="input-wrap">
				<label for="field-QUBES_liason"><?php echo Lang::txt('COM_PARTNERS_FIELD_QUBES_LIASON'); ?> </label>
				<input type="text" name="fields[QUBES_liason]" id="field-QUBES_liason" size="35" value="<?php echo $this->escape($this->row->get('QUBES_liason')); ?>" />
			</div>
			<!-- partner liason member number -->
			<div class="input-wrap">
				<label for="field-partner_liason"><?php echo Lang::txt('COM_PARTNERS_FIELD_PARTNER_LIASON'); ?></label>
				<input type="text" name="fields[partner_liason]" id="field-partner_liason" size="35" value="<?php echo $this->escape($this->row->get('partner_liason')); ?>" />
			</div>		
			<!--activites text box -->	
			<div class="input-wrap">
				<label for="field-about"><?php echo Lang::txt('COM_PARTNERS_FIELD_ACTIVITIES'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('fields[activities]', $this->escape($this->row->get('activities')), 50, 15, 'field-activities', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
			</div>
			<!-- about text box-->
			<div class="input-wrap">
				<label for="field-about"><?php echo Lang::txt('COM_PARTNERS_FIELD_ABOUT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('fields[about]', $this->escape($this->row->about('raw')), 50, 15, 'field-about', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
			</div>
		</fieldset>



		<fieldset class="adminform">
			<!-- partner types form for selecting partner type, radio button = can only select one partner type -->
			<legend><span><?php echo Lang::txt('COM_PARTNERS_PARTNER_TYPES'); ?></span></legend>

			<?php
			//for loop to display each partner type, the radio button will be checked if the partner_type (int) is equal to the partner_type id
			foreach ($this->partner_types as $partner_type) { ?>
				<?php
				$check = false;
					if ($this->row->get('partner_type') == $partner_type->get('id')){
						 
							$check =true;
						}
				
				?>
				<!-- changed here so that name=fields[partner-type] vs a partner_type field, thus during save task, we no longer need code to save which partner type we are, as everything is done through the fields[]-->
				<div class="input-wrap">
					<input class="option" type="radio" name="fields[partner_type]" id="fields-partner_type<?php echo $partner_type->get('id'); ?>" <?php if ($check) { echo ' checked="checked'; } ?> value="<?php echo $partner_type->get('id'); ?>" />
					<label for="<?php echo $partner_type->get('id'); ?>"><?php echo $this->escape($partner_type->get('internal')); ?></label>
				</div>
			<?php } ?>
		</fieldset>
	</div>


	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_PARTNERS_FIELD_ID'); ?>:</th>
					<td>
						<?php echo $this->row->get('id', 0); ?>
						<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->escape($this->row->get('id')); ?>" />
					</td>
				
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-state"><?php echo Lang::txt('COM_PARTNERS_FIELD_STATE'); ?>:</label><br />
				<select name="fields[state]" id="field-state">
					<option value="0"<?php if ($this->row->get('state') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
					<option value="1"<?php if ($this->row->get('state') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
					<option value="2"<?php if ($this->row->get('state') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
				</select>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>
	<!--These are important, especially box checked!!, allows you to use the edit/publish/delete buttons once you have checked something -->
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo Html::input('token'); ?>
</form>