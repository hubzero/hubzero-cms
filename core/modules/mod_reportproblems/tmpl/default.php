<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div id="help-pane">
	<div id="help-container" class="grid">
		<h1><?php echo Lang::txt('MOD_REPORTPROBLEMS_SUPPORT'); ?></h1>

		<div class="col span4">
			<h2><?php echo Lang::txt('MOD_REPORTPROBLEMS_SUPPORT_OPTIONS'); ?></h2>
			<ul>
				<li class="help-kb">
					<h3><a href="<?php echo Route::url('index.php?option=com_kb'); ?>"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_KB_TITLE'); ?></a></h3>
					<p><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_KB_DESC'); ?></p>
				</li>
				<li class="help-qa">
					<h3><a href="<?php echo Route::url('index.php?option=com_answers'); ?>"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_ANSWERS_TITLE'); ?></a></h3>
					<p><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_ANSWERS_DESC'); ?></p>
				</li>
				<li class="help-wish">
					<h3><a href="<?php echo Route::url('index.php?option=com_wishlist'); ?>"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_WISHLIST_TITLE'); ?></a></h3>
					<p><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_WISHLIST_DESC'); ?></p>
				</li>
				<li class="help-tickets">
					<h3><a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=display'); ?>"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_TICKETS_TITLE'); ?></a></h3>
					<p><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTION_TICKETS_DESC'); ?></p>
				</li>
			</ul>
		</div><!-- / .col span4 -->
		<div class="col span8 omega">
			<h2>
				<?php echo Lang::txt('MOD_REPORTPROBLEMS_SUBMIT_TICKET'); ?>
			</h2>
			<form method="post" action="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=save'); ?>" id="troublereport" enctype="multipart/form-data">
				<fieldset class="reporter">
					<label<?php if ($this->guestOrTmpAccount) { echo ' for="trLogin"'; } ?>>
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_LOGIN'); ?>: <span class="optional"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTIONAL'); ?></span>
						<?php if (!$this->guestOrTmpAccount) { ?>
							<input type="hidden" name="reporter[login]" id="trLogin" value="<?php echo $this->escape(User::get('username')); ?>" /><br /><span class="info-block"><?php echo $this->escape(User::get('username')); ?></span>
						<?php } else { ?>
							<input type="text" name="reporter[login]" id="trLogin" value="" />
						<?php } ?>
					</label>

					<label<?php if ($this->guestOrTmpAccount) { echo ' for="trName"'; } ?>>
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_NAME'); ?>: <span class="required"><?php echo Lang::txt('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
						<?php if (!$this->guestOrTmpAccount) { ?>
							<input type="hidden" name="reporter[name]" id="trName" value="<?php echo $this->escape(User::get('name')); ?>" /><br /><span class="info-block"><?php echo $this->escape(User::get('name')); ?></span>
						<?php } else { ?>
							<input type="text" name="reporter[name]" id="trName" value="" />
						<?php } ?>
					</label>

					<label<?php if ($this->guestOrTmpAccount) { echo ' for="trEmail"'; } ?>>
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_EMAIL'); ?>: <span class="required"><?php echo Lang::txt('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
						<?php if (!$this->guestOrTmpAccount) { ?>
							<input type="hidden" name="reporter[email]" id="trEmail" value="<?php echo $this->escape(User::get('email')); ?>" /><br /><span class="info-block"><?php echo $this->escape(User::get('email')); ?></span>
						<?php } else { ?>
							<input type="text" name="reporter[email]" id="trEmail" value="" />
						<?php } ?>
					</label>

					<?php
						$captchas = Event::trigger('support.onGetModuleCaptcha');

						if (count($captchas) > 0)
						{
							foreach ($captchas as $captcha)
							{
								echo $captcha;
							}
						}
					?>

					<label id="trBotcheck-label" for="trBotcheck">
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_BOTCHECK'); ?> <span class="required"><?php echo Lang::txt('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
						<input type="text" name="botcheck" id="trBotcheck" value="" />
					</label>
				</fieldset>
				<fieldset>
					<label for="trProblem">
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_PROBLEM'); ?>: <span class="required"><?php echo Lang::txt('MOD_REPORTPROBLEMS_REQUIRED'); ?></span>
						<textarea name="problem[long]" id="trProblem" rows="10" cols="40"></textarea>
					</label>

					<label for="trUpload">
						<?php echo Lang::txt('MOD_REPORTPROBLEMS_LABEL_ATTACH'); ?>: <span class="optional"><?php echo Lang::txt('MOD_REPORTPROBLEMS_OPTIONAL'); ?></span>
						<input type="file" name="upload" id="trUpload" />
						<span class="filetypes">(.<?php echo str_replace(',', ', .', $this->supportParams->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif')); ?>)</span>
						<script type="text/javascript">
							var _validFileExtensions = ['.<?php echo str_replace(',', "','.", $this->supportParams->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif')); ?>'];
						</script>
					</label>

					<input type="hidden" name="problem[topic]" value="???" />
					<input type="hidden" name="problem[short]" value="" />
					<input type="hidden" name="problem[referer]" value="<?php echo $this->escape($this->referrer); ?>" />
					<input type="hidden" name="problem[tool]" value="" />
					<input type="hidden" name="problem[os]" value="<?php echo $this->escape($this->os); ?>" />
					<input type="hidden" name="problem[osver]" value="<?php echo $this->escape($this->os_version); ?>" />
					<input type="hidden" name="problem[browser]" value="<?php echo $this->escape($this->browser); ?>" />
					<input type="hidden" name="problem[browserver]" value="<?php echo $this->escape($this->browser_ver); ?>" />
					<input type="hidden" name="verified" value="<?php echo $this->verified; ?>" />
					<input type="hidden" name="reporter[org]" value="<?php echo (!User::isGuest()) ? $this->escape(User::get('org')) : ''; ?>" />
					<input type="hidden" name="option" value="com_support" />
					<input type="hidden" name="controller" value="tickets" />
					<input type="hidden" name="task" value="save" />
					<input type="hidden" name="no_html" value="1" />

					<?php echo Html::input('token'); ?>
				</fieldset>
				<div class="submit"><input type="submit" id="send-form" value="<?php echo Lang::txt('MOD_REPORTPROBLEMS_SUBMIT'); ?>" /></div>
			</form>
			<div id="trSending">
				<!-- Loading animation container -->
				<div class="rp-loading">
					<!-- We make this div spin -->
					<div class="rp-spinner">
						<!-- Mask of the quarter of circle -->
						<div class="rp-mask">
							<!-- Inner masked circle -->
							<div class="rp-masked-circle"></div>
						</div>
					</div>
				</div>
			</div>
			<div id="trSuccess">
			</div>
		</div><!-- / .col span8 omega -->
	</div><!-- / #help-container -->
</div><!-- / #help-pane -->
