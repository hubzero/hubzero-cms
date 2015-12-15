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

$this->css();

$tmpl = Request::getVar('tmpl', '');

if ($tmpl != 'component')
{
	Toolbar::title(Lang::txt('COM_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_FOR_PUB') . ' #' . $this->pub->get('id') . ' (v.' . $this->pub->get('version_label') . ')', 'groups.png');
	Toolbar::save('savecontent');
	Toolbar::cancel();
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php return; } ?>
<p class="crumbs"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER'); ?></a> &raquo; <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $this->pub->get('id') . '&version=' . $this->pub->get('version_number')); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' #' . $this->pub->get('id'); ?></a> &raquo; <?php echo Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_INFO'); ?></p>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
<?php if ($tmpl == 'component') { ?>
	<fieldset>
		<div style="float: right">
			<?php if (!$this->getError()) { ?>
			<button type="button" onclick="submitbutton('savecontent');"><?php echo Lang::txt( 'JSAVE' );?></button>
			<?php } ?>
			<button type="button" onclick="window.parent.document.getElementById('sbox-window').close();"><?php echo Lang::txt( 'Cancel' );?></button>
		</div>
		<div class="configuration" >
			<?php echo Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT') ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_PUBLICATIONS_EDIT_CONTENT_INFO'); ?></span></legend>

				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="no_html" value="<?php echo ($tmpl == 'component') ? '1' : '0'; ?>">
				<input type="hidden" name="id" value="<?php echo $this->pub->get('id'); ?>" />
				<input type="hidden" name="task" value="savecontent" />
				<input type="hidden" name="version" value="<?php echo $this->pub->get('version_number'); ?>" />

				<?php if ($this->getError()) {
					echo '<p class="error">' . $this->getError() . '</p>';
				} else { ?>
					<input type="hidden" name="el" value="<?php echo $this->elementId; ?>" />
					<?php
					$element = $this->element->element;
					$block   = $this->element->block;
					$elName  = 'element' . $this->elementId;
					// Customize title
					$defaultTitle = $element->params->title
									? str_replace('{pubtitle}', $this->pub->title,
									$element->params->title) : NULL;
					$defaultTitle = $element->params->title
									? str_replace('{pubversion}', $this->pub->version_label,
									$defaultTitle) : NULL;

					$attachments = $this->pub->_attachments;
					$attachments = isset($attachments['elements'][$this->elementId])
								 ? $attachments['elements'][$this->elementId] : NULL;

					// Get version params and extract bundle name
					$bundleName = $this->pub->params->get($elName . 'bundlename', $defaultTitle);

					$multiZip = (isset($element->params->typeParams->multiZip)
									&& $element->params->typeParams->multiZip == 0)
									? false : true;

					?>
					<?php if (count($attachments) > 1 && $multiZip) { ?>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_BUNDLE_NAME'); ?>:</label>
						<input type="text" name="params[element<?php echo $this->elementId; ?>bundlename]" maxlength="250" value="<?php echo $bundleName; ?>" />
					</div>
					<?php } ?>
					<?php if ($attachments) { ?>
						<?php foreach ($attachments as $attach) { ?>
							<div class="input-wrap withdivider">
								<p>[<?php echo $attach->type; ?>] <?php echo $attach->path; ?></p>
								<label><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ATTACHMENT_TITLE'); ?>:</label>
								<input type="text" name="attachments[<?php echo $attach->id; ?>][title]" maxlength="250" value="<?php echo $attach->title; ?>" />
							</div>
						<?php } ?>
					<?php } else { ?>
						<p class="notice"><?php echo Lang::txt('COM_PUBLICATIONS_NO_CONTENT'); ?></p>
					<?php } ?>
				<?php } ?>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_ELEMENT_ID'); ?></td>
						<td>
							<?php echo $this->elementId; ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_ELEMENT_TYPE'); ?></td>
						<td>
							<?php echo $element->params->type; ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE'); ?></td>
						<td>
							<?php echo $element->params->role == 1 ? Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE_PRIMARY') : Lang::txt('COM_PUBLICATIONS_ELEMENT_ROLE_SECOND'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
</form>