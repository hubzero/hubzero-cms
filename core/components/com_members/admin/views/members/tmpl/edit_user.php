<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$name       = stripslashes($this->profile->get('name'));
$surname    = stripslashes($this->profile->get('surname'));
$givenName  = stripslashes($this->profile->get('givenName'));
$middleName = stripslashes($this->profile->get('middleName'));

if (!$surname)
{
	$bits = explode(' ', $name);
	$surname = array_pop($bits);
	if (count($bits) >= 1)
	{
		$givenName = array_shift($bits);
	}
	if (count($bits) >= 1)
	{
		$middleName = implode(' ', $bits);
	}
}

$incomplete = false;
$authenticator = 'hub';
if (substr($this->profile->get('email'), -8) == '@invalid')
{
	$authenticator = Lang::txt('COM_MEMBERS_UNKNOWN');
	if ($lnk = Hubzero\Auth\Link::find_by_id(abs($this->profile->get('username'))))
	{
		$domain = Hubzero\Auth\Domain::find_by_id($lnk->auth_domain_id);
		$authenticator = $domain->authenticator;
	}
	$incomplete = true;
}
?>
<div class="grid">
	<div class="col span7">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('Account Details'); ?></span></legend>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME_HINT'); ?>">
				<label id="field_username-lbl" for="field_username" class="required"><?php echo Lang::txt('COM_MEMBERS_FIELD_USERNAME'); ?> <span class="required star"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[username]" id="field_username" value="<?php echo $this->escape($this->profile->get('username')); ?>" class="required<?php if ($this->profile->get('id')) { echo ' readonly" readonly="readonly'; } ?>" />
			</div>

			<div class="input-wrap">
				<label id="field_email-lbl" for="field_email" class="required"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL'); ?> <span class="required star"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
				<input type="text" name="fields[email]" class="validate-email required" id="field_email" value="<?php echo $this->escape($this->profile->get('email')); ?>" />
			</div>

			<fieldset>
				<legend><span><?php echo Lang::txt('COM_MEMBERS_FIELD_NAME'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-givenName"><?php echo Lang::txt('COM_MEMBERS_FIELD_FIRST_NAME'); ?>:</label><br />
					<input type="text" name="fields[givenName]" id="field-givenName" value="<?php echo $this->escape($givenName); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-middleName"><?php echo Lang::txt('COM_MEMBERS_FIELD_MIDDLE_NAME'); ?>:</label><br />
					<input type="text" name="fields[middleName]" id="field-middleName" value="<?php echo $this->escape($middleName); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-surname"><?php echo Lang::txt('COM_MEMBERS_FIELD_LAST_NAME'); ?>:</label><br />
					<input type="text" name="fields[surname]" id="field-surname" value="<?php echo $this->escape($surname); ?>" />
				</div>
			</fieldset>

			<div class="grid">
				<div class="col span6">
					<div class="input-wrap">
						<label for="field-access"><?php echo Lang::txt('COM_MEMBERS_FIELD_ACCESS_LEVEL'); ?>:</label>
						<select name="fields[access]" id="field-access">
							<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->profile->get('access')); ?>
						</select>
					</div>
				</div>
				<div class="col span6">
					<div class="input-wrap">
						<?php
							//mailPreferenceOption
							$options = array(
								'-1' => Lang::txt('COM_MEMBERS_PROFILE_FORM_SELECT_FROM_LIST'),
								'1'  => Lang::txt('JYES'),
								'0'  => Lang::txt('JNO')
							);
						?>
						<label for="field-sendEmail"><?php echo Lang::txt('COM_MEMBERS_FIELD_MAIL_PREFERENCE'); ?></label>
						<select name="fields[sendEmail]" id="field-sendEmail">
							<?php foreach ($options as $key => $value) : ?>
								<?php $sel = ($key == $this->profile->get('sendEmail')) ? ' selected="selected"' : ''; ?>
								<option<?php echo $sel; ?> value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>

			<div class="input-wrap">
				<label for="field-homeDirectory" class="required"><?php echo Lang::txt('COM_MEMBERS_FIELD_HOMEDIRECTORY'); ?></label>
				<input type="text" name="fields[homeDirectory]" id="field-homeDirectory" value="<?php echo $this->escape($this->profile->get('homeDirectory')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-loginShell"><?php echo Lang::txt('COM_MEMBERS_FIELD_LOGINSHELL'); ?></label>
				<input type="text" name="fields[loginShell]" id="field-loginShell" value="<?php echo $this->escape($this->profile->get('loginShell')); ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-ftpShell"><?php echo Lang::txt('COM_MEMBERS_FIELD_FTPSHELL'); ?></label>
				<input type="text" name="fields[ftpShell]" id="field-ftpShell" value="<?php echo $this->escape($this->profile->get('ftpShell')); ?>" />
			</div>
		</fieldset>

		<fieldset id="user-groups" class="adminform">
			<legend><span><?php echo Lang::txt('Assigned Access Groups'); ?></span></legend>

			<div class="input-wrap">
				<?php
				$groups = array();
				foreach ($this->profile->accessgroups()->rows() as $g)
				{
					$groups[] = $g->get('group_id');
				}
				echo Html::access('usergroups', 'fields[accessgroups]', $groups, true); ?>
			</div>
		</fieldset>
	</div>
	<div class="col span5">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_ID'); ?></th>
					<td>
						<?php echo $this->profile->get('id'); ?>
						<input type="hidden" name="fields[id]" value="<?php echo $this->profile->get('id'); ?>" />
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_REGISTERDATE'); ?></th>
					<th><?php echo $this->profile->get('registerDate'); ?></th>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_LASTVISITDATE'); ?></th>
					<th><?php echo (!$this->profile->get('lastvisitDate') || $this->profile->get('lastvisitDate') == '0000-00-00 00:00:00' ? Lang::txt('COM_MEMBERS_NEVER') : $this->profile->get('lastvisitDate')); ?></th>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_FIELD_MODIFIED'); ?></th>
					<th><?php echo (!$this->profile->get('modifiedDate') || $this->profile->get('modifiedDate') == '0000-00-00 00:00:00' ? Lang::txt('COM_MEMBERS_NEVER') : $this->profile->get('modifiedDate')); ?></th>
				</tr>
				<?php if ($incomplete) : ?>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_AUTHENTICATOR'); ?></th>
						<th><?php echo $authenticator; ?></th>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_MEMBERS_AUTHENTICATOR_STATUS'); ?></th>
						<th><?php echo Lang::txt('COM_MEMBERS_INCOMPLETE'); ?></th>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_MEMBERS_STATUS'); ?></span></legend>

			<div class="input-wrap">
				<label id="field_usageAgreement-lbl" for="field-usageAgreement"><?php echo Lang::txt('COM_MEMBERS_FIELD_USAGE_AGREEMENT'); ?></label>
				<fieldset id="field-usageAgreement" class="radio">
					<ul>
						<li><input type="radio" id="field-usageAgreement0" name="fields[usageAgreement]" value="0"<?php if ($this->profile->get('usageAgreement') == 0) { echo ' checked="checked"'; } ?> /><label for="field-usageAgreement0"><?php echo Lang::txt('JNo'); ?></label></li>
						<li><input type="radio" id="field-usageAgreement1" name="fields[usageAgreement]" value="1"<?php if ($this->profile->get('usageAgreement') == 1) { echo ' checked="checked"'; } ?> /><label for="field-usageAgreement1"><?php echo Lang::txt('JYes'); ?></label></li>
					</ul>
				</fieldset>
			</div>

			<div class="input-wrap">
				<label id="field-block-lbl" for="field-block"><?php echo Lang::txt('Block this User'); ?></label>
				<fieldset id="field-block" class="radio">
					<ul>
						<li><input type="radio" id="field-block0" name="fields[block]" value="0"<?php if ($this->profile->get('block') == 0) { echo ' checked="checked"'; } ?> /><label for="field-block0"><?php echo Lang::txt('JNo'); ?></label></li>
						<li><input type="radio" id="field-block1" name="fields[block]" value="1"<?php if ($this->profile->get('block') == 1) { echo ' checked="checked"'; } ?> /><label for="field-block1"><?php echo Lang::txt('JYes'); ?></label></li>
					</ul>
				</fieldset>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('Approved User::User approval status. Users not approved are as such because registration requires admin approval.'); ?>">
				<label id="field_approved-lbl" for="field_approved"><?php echo Lang::txt('Approved User'); ?></label>
				<select id="field_approved" name="fields[approved]">
					<option value="0"<?php if ($this->profile->get('approved') == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Not approved'); ?></option>
					<option value="1"<?php if ($this->profile->get('approved') == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Manually approved'); ?></option>
					<option value="2"<?php if ($this->profile->get('approved') == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Automatically approved'); ?></option>
				</select>
				<span class="hint"><?php echo Lang::txt('Approved User::User approval status. Users not approved are as such because registration requires admin approval.'); ?></span>
			</div>

			<div class="input-wrap">
				<?php if ($this->profile->get('email')): ?>
					<?php
					if ($this->profile->get('activation') == 1)
					{
						$confirmed = '<label for="activation"><input type="checkbox" name="activation" id="activation" value="1" checked="checked" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED') . '</label>';
					}
					elseif ($this->profile->get('activation') == 2)
					{
						$confirmed = Lang::txt('COM_MEMBERS_FIELD_EMAIL_GRANDFATHERED') . '<input type="hidden" name="activation" id="activation" value="2" />';
					}
					elseif ($this->profile->get('activation') == 3)
					{
						$confirmed = Lang::txt('COM_MEMBERS_FIELD_EMAIL_DOMAIN_SUPPLIED') . '<input type="hidden" name="activation" id="activation" value="3" />';
					}
					elseif ($this->profile->get('activation') < 0)
					{
						if ($this->profile->get('email'))
						{
							$confirmed  = Lang::txt('COM_MEMBERS_FIELD_EMAIL_AWAITING_CONFIRMATION');
							$confirmed .= '[code: ' . -$this->profile->get('activation') . '] <label for="activation"><input type="checkbox" name="activation" id="activation" value="1" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') . '</label>';
						}
						else
						{
							$confirmed  = Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE');
						}
					}
					else
					{
						$confirmed  = '[' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_UNKNOWN_STATUS') . '] <label for="activation"><input type="checkbox" name="activation" id="activation" value="1" /> ' . Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM') . '</label>';
					}
					echo $confirmed;
					?>
				<?php else: ?>
					<span style="color:#c00;"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_NONE_ON_FILE'); ?></span><br />
					<input type="checkbox" name="activation" id="activation" value="1" />
					<label for="activation"><?php echo Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRM'); ?></label>
				<?php endif; ?>
			</div>

			<?php if ($this->profile->get('id') && Plugin::isEnabled('system', 'spamjail')) : ?>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_SPAM_COUNT_HINT'); ?>">
					<label id="field_approved-lbl" for="field-reputation"><?php echo Lang::txt('COM_MEMBERS_SPAM_COUNT'); ?></label>
					<div class="input-modal">
						<span class="input-cell">
							<input type="text" name="spam_count" id="field-reputation" value="<?php echo $this->escape($this->profile->reputation->get('spam_count', 0)); ?>" />
						</span>
						<span class="input-cell">
							<a class="button" href="#field-reputation" onclick="document.getElementById('field-reputation').value='0';Joomla.submitbutton('apply');"><?php echo Lang::txt('COM_MEMBERS_RESET'); ?></a>
						</span>
					</div>
					<?php if ($this->profile->reputation->get('spam_count', 0) > Plugin::params('system', 'spamjail')->get('user_count', 10)) : ?>
						<p class="warning"><?php echo Lang::txt('COM_MEMBERS_SPAM_COUNT_EXCEEDED'); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</fieldset>

		<?php
		// Create a new form
		$data = new Hubzero\Config\Registry();
		$data->set('params', $this->profile->params->toArray());
		$form = new Hubzero\Form\Form('fields', array('control' => 'fields'));
		$form->load(Hubzero\Form\Form::getXML(Component::path('com_members') . DS . 'models' . DS . 'forms' . DS . 'user.xml', true));
		$form->bind($data);

		$fieldsets = $form->getFieldsets();

		echo Html::sliders('start');

		foreach ($fieldsets as $fieldset) :
			if ($fieldset->name == 'user_details') :
				continue;
			endif;
			echo Html::sliders('panel', Lang::txt($fieldset->label), $fieldset->name);
			?>
			<fieldset class="panelform">
				<?php foreach ($form->getFieldset($fieldset->name) as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<div class="input-wrap">
							<?php echo $field->label; ?>
							<?php echo $field->input; ?>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>
		<?php endforeach; ?>
		<?php echo Html::sliders('end'); ?>
	</div>
</div>
