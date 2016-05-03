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
?>
<fieldset class="adminform">
	<legend><span><?php echo Lang::txt('COM_MEMBERS_FIELD_PASSWORD'); ?></span></legend>

	<div class="input-wrap">
		<?php echo Lang::txt('COM_MEMBERS_PASSWORD_CURRENT'); ?>:
		<input type="text" name="profile[currentpassword]" disabled="disabled" <?php echo ($this->profile->get('password')) ? "value=\"{$this->profile->get('password')}\"" : 'placeholder="' . Lang::txt('no local password set') . '"'; ?> />
	</div>
	<div class="input-wrap">
		<label for="newpass"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW'); ?>:</label>
		<input type="password" name="newpass" id="newpass" value="" />
		<p class="warning"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_NEW_WARNING'); ?></p>
		<?php if (count($this->password_rules) > 0) : ?>
			<?php $this->css('password.css'); ?>
			<script type="text/javascript">
				/*jQuery(document).ready(function ( $ )
				{
					var password = $('#newpass'),
					checkPass    = function() {
						// Create an ajax call to check the potential password
						$.ajax({
							url: "/api/members/checkpass",
							type: "POST",
							data: "password1="+password.val(),
							dataType: "html",
							cache: false,
							success: function ( html ) {
								if (html.length > 0 && password.val() != '')
								{
									$('.passrules').html(html);
								}
								else
								{
									// Probably deleted password, so reset classes
									$('.passrules').find('li').removeClass('error passed').addClass('empty');
								}
							}
						});
					};

					password.on('keyup', checkPass);
				});*/
			</script>
			<div><?php echo Lang::txt('COM_MEMBERS_PASSWORD_RULES'); ?>:</div>
			<ul class="passrules">
				<?php foreach ($this->password_rules as $rule) : ?>
					<?php if (!empty($rule)) : ?>
						<?php if ($this->validated && is_array($this->validated) && in_array($rule, $this->validated)) : ?>
							<li class="pass-error"><?php echo $rule; ?></li>
						<?php elseif ($this->validated) : ?>
							<li class="pass-passed"><?php echo $rule; ?></li>
						<?php else : ?>
							<li class="pass-empty"><?php echo $rule; ?></li>
						<?php endif; ?>
					<?php endif; ?>
				<?php endforeach ?>
			</ul>
		<?php endif; ?>
	</div>
	<div class="input-wrap">
		<label id="field_password2-lbl" for="field_password2"><?php echo Lang::txt('Confirm Password'); ?></label>
		<input type="password" name="fields[password2]" id="field_password2" value="" autocomplete="off" class="inputbox validate-password" />
	</div>
	<!-- <div class="input-wrap" data-hint="<?php echo Lang::txt('Number of password resets since last reset date'); ?>">
		<label id="jform_resetCount-lbl" for="jform_resetCount"><?php echo Lang::txt('Password Reset Count'); ?></label>
		<input type="text" name="jform[resetCount]" id="jform_resetCount" value="0" class="readonly" readonly="readonly"/>
	</div> -->
	<div class="input-wrap">
		<label title="shadowLastChange"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_LAST_CHANGE'); ?>:</label>
		<?php
			if (is_object($this->password) && $this->password->get('shadowLastChange'))
			{
				$shadowLastChange = $this->password->get('shadowLastChange')*86400;
				echo date("Y-m-d", $shadowLastChange);
				echo " ({$this->password->get('shadowLastChange')})";
				echo " - " . intval((time()/86400) - ($shadowLastChange/86400)) . " days ago";
			}
			else
			{
				echo Lang::txt('COM_MEMBERS_NEVER');
			}
		?>
	</div>
	<div class="input-wrap">
		<label title="shadowMax" class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_MAX'); ?>:</label>
		<input type="text" name="shadowMax" value="<?php echo $this->escape($this->password->get('shadowMax')); ?>" />
	</div>
	<div class="input-wrap">
		<label title="shadowWarning" class="key"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_WARNING'); ?>:</label>
		<input type="text" name="shadowWarning" value="<?php echo $this->escape($this->password->get('shadowWarning')); ?>" />
	</div>
	<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?>">
		<label title="shadowExpire"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE'); ?>:</label>
		<input type="text" name="shadowExpire" value="<?php echo $this->escape($this->password->get('shadowExpire')); ?>" />
		<span class="hint"><?php echo Lang::txt('COM_MEMBERS_PASSWORD_SHADOW_EXPIRE_HINT'); ?></span>
	</div>
</fieldset>
