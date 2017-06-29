<?php
// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Partners\Helpers\Permissions::getActions('partner');
//use Components\Partners\Models\Partner;
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
	if ($('#field-name').val() == '' || $('#field-date_joined').val() == ''|| $('#field-site_url').val() == ''
		|| $('#field-activities').val() == '' || $('#field-about').val() == '' ){
		alert("<?php echo Lang::txt('COM_PARTNERS_ERROR_MISSING_FIELDS'); ?>");
	} else {
		<?php echo $this->editor()->save('text'); ?>

		submitform(pressbutton);
	}
}

</script>
<!--Setting enctype to multipart encoding allows us to access the image seperately with a request::getvar in partners -->
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form" enctype="multipart/form-data">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>
			<!--to access values from database, need to use $this->escape($this->row->get('variable name in database')) -->
			
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_HINT_NAME'); ?>">
				<label for="field-name"><?php echo Lang::txt('COM_PARTNERS_FIELD_NAME'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>	
				<input type="text" name="fields[name]" id="field-name" size="35" value="<?php echo $this->escape($this->row->get('name')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_PARTNERS_HINT_NAME'); ?></span>
			</div>

			<!--Date joined-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_HINT_DATE_JOINED'); ?>">
					<label for="field-date_joined"><?php echo Lang::txt('COM_PARTNERS_FIELD_DATE_JOINED'); ?><span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<!-- just had to simply change the type to date here to get our calendar to show up-->
					<input class = "input-wrap" type="date" name="fields[date_joined]" id="field-date_joined" size="45" value="<?php echo $this->escape($this->row->get('date_joined')); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_PARTNERS_HINT_DATE_JOINED'); ?></span>
				</div>

			<!--URL to partners website-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_HINT_LINK'); ?>">
				<label for="field-site_url"><?php echo Lang::txt('COM_PARTNERS_FIELD_LINK'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[site_url]" id="field-site_url" size="35" value="<?php echo $this->escape($this->row->get('site_url')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_PARTNERS_FIELD_LINK'); ?></span>
			</div>
			<!--Logo -->
			<div class="input-wrap">
					<label for="logo-image"><?php echo Lang::txt('COM_PARTNERS_FIELD_LOGO'); ?>:</label><br />
					<input type="file" name="logo-image" id="logo-image" />
				</div>
			
			<!--GROUP CN -->
			<div class="input-wrap">
					<label for="fields-groups_cn"><?php echo Lang::txt('COM_PARTNERS_FIELD_GROUP_CN'); ?></label>
					<select name="fields[groups_cn]" id="fields-groups_cn">
						<?php if ($this->row->get("groups_cn") == '') { ?> <option value = "">select</option> <?php }?>
						<?php foreach ($this->grouprows as $val) { ?>
							<option<?php if ($this->row->get('groups_cn') == $val->cn) { echo ' selected="selected"'; } ?> value="<?php echo $this->escape($val->cn); ?>"><?php echo $this->escape($val->cn); ?></option>
						<?php } ?>
					</select>
				</div>
			<!--QUBES Liason-->
				<div class="input-wrap">
					<label for="fields-QUBES_liason"><?php echo Lang::txt('COM_PARTNERS_FIELD_QUBES_LIASON'); ?></label>
					<select name="fields[QUBES_liason]" id="fields-QUBES_liason">
						<?php if ($this->row->get("QUBES_liason") == '') { ?> <option value = "">select</option> <?php }?>
						<?php foreach ($this->member_names as $val) { ?>
							<option<?php if ($this->row->get('QUBES_liason') == $val->text) { echo ' selected="selected"'; }
							?> value="<?php echo  $this->escape($val->text); ?>">
							<?php echo $this->escape($val->text); ?></option>
						<?php } ?>
					</select>
				</div>
				<!--Partner Liason-->
				<div class="input-wrap">
					<label for="fields-partner_liason"><?php echo Lang::txt('COM_PARTNERS_FIELD_PARTNER_LIASON'); ?></label>
					<select name="fields[partner_liason]" id="fields-QUBES_liason">
					<?php if ($this->row->get("partner_liason") == '') { ?> <option value = "">select</option> <?php }?>
						<?php foreach ($this->member_names as $val) { ?>
							<option<?php if ($this->row->get('partner_liason') == $val->text) { echo ' selected="selected"'; }
							?> value="<?php echo  $this->escape($val->text); ?>">
							<?php echo $this->escape($val->text); ?></option>
						<?php } ?>
					</select>
				</div>

			<!--Twitter Handle-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_HINT_TWITTER_HANDLE'); ?>">
				<label for="field-twitter_handle"><?php echo Lang::txt('COM_PARTNERS_FIELD_TWITTER_HANDLE'); ?> </label>
				<input type="text"  name="fields[twitter_handle]" id="field-twitter_handle" size="35" value="<?php echo $this->escape($this->row->get('twitter_handle')); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_PARTNERS_HINT_TWITTER_HANDLE'); ?></span>
			</div>

				
			<!--activites text box -->	
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_HINT_ACTIVITIES'); ?>">
				<label for="field-about"><?php echo Lang::txt('COM_PARTNERS_FIELD_ACTIVITIES'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('fields[activities]', $this->escape($this->row->get('activities')), 50, 15, 'field-activities', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				<span class="hint"><?php echo Lang::txt('COM_PARTNERS_HINT_ACTIVITIES'); ?></span>
			</div>
			<!-- about text box-->
			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_PARTNERS_FIELD_ABOUT'); ?>">
				<label for="field-about"><?php echo Lang::txt('COM_PARTNERS_FIELD_ABOUT'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<?php echo $this->editor('fields[about]', $this->escape($this->row->about('raw')), 50, 15, 'field-about', array('class' => 'minimal no-footer', 'buttons' => false)); ?>
				<span class="hint"><?php echo Lang::txt('COM_PARTNERS_HINT_ABOUT'); ?></span>
			</div>
		</fieldset>



		<fieldset class="adminform" >
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
					<input class="option" type="radio" name="fields[partner_type]" id="fields-partner_type<?php echo $partner_type->get('id'); ?>" <?php if ($check) { echo ' checked="checked"'; } ?> value="<?php echo $partner_type->get('id'); ?>" />
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

	<!--display image here -->
	<?php if ($this->row->get('logo_img', false)) : ?>
				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_PARTNERS_CURRENT_IMG'); ?></span></legend>
					<?php $image = new \Hubzero\Image\Processor(PATH_ROOT . DS . ltrim('app/site/media/images/partners/' . $this->row->get('logo_img'), DS)); ?>
					<legend><span><?php echo Lang::txt($this->row->get('logo_img')); ?></span></legend>
					<?php if (count($image->getErrors()) == 0) : ?>
						<?php $image->resize(500); ?>
						<div style="padding: 10px;"><img src="<?php echo $image->inline(); ?>" alt="logo image" /></div>
					<?php endif; ?>
				</fieldset>
			<?php endif; ?>

	<div class="clr"></div>
	<!--These are important, especially box checked!!, allows you to use the edit/publish/delete buttons once you have checked something -->
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	
	<?php echo Html::input('token'); ?>  
</form>