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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Get parameters
$rparams = new \Hubzero\Config\Registry($this->resource->params);
$params = $this->config;
$params->merge($rparams);

$this->css('create.css')
     ->js('create.js');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-add add btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=draft'); ?>">
				<?php echo Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION'); ?>
			</a>
		</p>
	</div><!-- / #content-header -->
</header><!-- / #content-header -->

<section class="main section">
	<?php
		$this->view('steps')
		     ->set('option', $this->option)
		     ->set('step', $this->step)
		     ->set('steps', $this->steps)
		     ->set('id', $this->id)
		     ->set('resource', $this->resource)
		     ->set('progress', $this->progress)
		     ->display();
	?>

<?php if ($this->getError()) { ?>
	<p class="warning"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=' . $this->task); ?>" method="post" id="hubForm">
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->step; ?>" />

<?php if ($this->progress['submitted'] == 1) { ?>
		<div class="explaination">
			<p class="help">
				<?php echo Lang::txt('COM_CONTRIBUTE_PASSED_REVIEW'); ?> <a href="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id); ?>"><?php echo Lang::txt('COM_CONTRIBUTE_VIEW_HERE'); ?></a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_LICENSING_LEGEND'); ?></legend>

			<label for="license">
				<?php echo Lang::txt('COM_CONTRIBUTE_LICENSE_LABEL'); ?>
				<select name="license" id="license">
					<option value=""><?php echo Lang::txt('COM_CONTRIBUTE_SELECT_LICENSE'); ?></option>
				<?php
				$l = array();
				$c = false;
				$preview = Lang::txt('COM_CONTRIBUTE_LICENSE_PREVIEW');
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
					?>
					<option value="custom"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Custom'); ?></option>
					<?php
						$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(nl2br($license->text)) . '" />';
						$c = $this->escape(nl2br($license->text));
					}
				}
				if (!$c && $this->config->get('cc_license_custom'))
				{
					?>
					<option value="custom"><?php echo Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE'); ?></option>
					<?php
					$c = $this->escape(Lang::txt('COM_CONTRIBUTE_ENTER_LICENSE_HERE'));
					$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(Lang::txt('COM_CONTRIBUTE_ENTER_LICENSE_HERE')) . '" />';
				}
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
						continue;
					}
					else
					{
					?>
					<option value="<?php echo $this->escape($license->name); ?>"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo $this->escape($license->title); ?></option>
					<?php
					}
					$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
					if ($params->get('license') == $license->name)
					{
						$preview = nl2br($this->escape($license->text));
					}
				}
				?>
				</select>
				<div id="license-preview" style="display:none;"><?php echo $preview; ?></div>
				<?php echo implode("\n", $l); ?>
			</label>
			<?php if ($this->config->get('cc_license_custom')) { ?>
			<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $c; ?></textarea>
			<?php } ?>

			<input type="hidden" name="published" value="1" />
			<input type="hidden" name="authorization" value="1" />
		</fieldset><div class="clear"></div>
		<p class="submit">
			<input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_SAVE'); ?>" />
		</p>
	</form>
<?php } else { ?>
		<div class="explaination">
			<h4><?php echo Lang::txt('COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT'); ?></h4>
		<?php if ($this->config->get('autoapprove', 0) != 1) { ?>
			<p>
				<?php echo Lang::txt(
					'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_ANSWER',
					'<a href="' . Route::url('index.php?option=' . $this->option) . '">' . Lang::txt('resources') . '</a>',
					'<a href="' . Route::url('index.php?option=com_whatsnew') . '">' . Lang::txt('What\'s New') . '</a>'
				); ?>
			</p>
		<?php } else { ?>
			<p>
				<?php echo Lang::txt(
					'COM_CONTRIBUTE_WHAT_HAPPENS_AFTER_SUBMIT_AUTOAPPROVED_ANSWER',
					'<a href="' . Route::url('index.php?option=' . $this->option) . '">' . Lang::txt('resources') . '</a>',
					'<a href="' . Route::url('index.php?option=com_whatsnew') . '">' . Lang::txt('What\'s New') . '</a>'
				); ?>
			</p>
		<?php } ?>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CONTRIBUTE_AUTHORIZATION_LEGEND'); ?></legend>

			<label for="authorization">
				<input class="option" type="checkbox" name="authorization" id="authorization" value="1" />
				<span class="required"><?php echo Lang::txt('COM_CONTRIBUTE_REQUIRED'); ?></span>
				<?php echo Lang::txt(
					'COM_CONTRIBUTE_AUTHORIZATION_LABEL',
					Config::get('sitename'),
					Config::get('sitename'),
					Config::get('sitename')
				); ?><br /><br />
				<?php echo Lang::txt('COM_CONTRIBUTE_AUTHORIZATION_LINKS_LABEL'); ?>
				<br /><br />
				<?php echo Lang::txt(
					'COM_CONTRIBUTE_AUTHORIZATION_MUST_ATTRIBUTE',
					Config::get('sitename'),
					'<a class="popup 760x560" href="' . Request::base(true) . '/legal/license">' . Lang::txt('COM_CONTRIBUTE_THE_FULL_LICENSE') . '</a>'
				); ?>
			</label>
	<?php if ($this->config->get('cc_license')) { ?>
			<label for="license">
				<?php echo Lang::txt('COM_CONTRIBUTE_LICENSE_LABEL'); ?>
				<select name="license" id="license">
					<option value=""><?php echo Lang::txt('COM_CONTRIBUTE_SELECT_LICENSE'); ?></option>
			<?php
				$l = array();
				$c = false;
				$preview = Lang::txt('COM_CONTRIBUTE_LICENSE_PREVIEW');
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
					?>
						<option value="custom"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('Custom'); ?></option>
					<?php
						$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(nl2br($license->text)) . '" />';
						$c = $this->escape(nl2br($license->text));
					}
				}
				if (!$c && $this->config->get('cc_license_custom'))
				{
					?>
						<option value="custom"><?php echo Lang::txt('COM_CONTRIBUTE_CUSTOM_LICENSE'); ?></option>
					<?php
					$c = $this->escape(Lang::txt('COM_CONTRIBUTE_ENTER_LICENSE_HERE'));
					$l[] = '<input type="hidden" id="license-custom" value="' . $this->escape(Lang::txt('COM_CONTRIBUTE_ENTER_LICENSE_HERE')) . '" />';
				}
				foreach ($this->licenses as $license)
				{
					if (substr($license->name, 0, 6) == 'custom')
					{
						continue;
					}
					else
					{
					?>
						<option value="<?php echo $this->escape($license->name); ?>"<?php if ($params->get('license') == $license->name) { echo ' selected="selected"'; } ?>><?php echo $this->escape($license->title); ?></option>
					<?php
					}
					$l[] = '<input type="hidden" id="license-' . $this->escape($license->name) . '" value="' . $this->escape(nl2br($license->text)) . '" />';
					if ($params->get('license') == $license->name)
					{
						$preview = nl2br($this->escape($license->text));
					}
				}
			?>
				</select>
				<div id="license-preview" style="display:none;"><?php echo $preview; ?></div>
				<?php echo implode("\n", $l); ?>
			</label>
		<?php if ($this->config->get('cc_license_custom')) { ?>
			<textarea name="license-text" id="license-text" cols="35" rows="10" style="display:none;"><?php echo $c; ?></textarea>
		<?php } ?>
	<?php } ?>

			<input type="hidden" name="published" value="0" />
		</fieldset><div class="clear"></div>
		<div class="submit">
			<input type="submit" value="<?php echo Lang::txt('COM_CONTRIBUTE_SUBMIT_CONTRIBUTION'); ?>" />
		</div>
	</form>

	<h1 id="preview-header"><?php echo Lang::txt('COM_CONTRIBUTE_REVIEW_PREVIEW'); ?></h1>
	<div id="preview-pane">
		<iframe id="preview-frame" name="preview-frame" width="100%" frameborder="0" src="<?php echo Route::url('index.php?option=com_resources&id=' . $this->id . '&tmpl=component&mode=preview'); ?>"></iframe>
	</div>
<?php } ?>
</section><!-- / .main section -->
