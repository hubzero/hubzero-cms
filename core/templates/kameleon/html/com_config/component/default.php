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

// No direct access
defined('_HZEXEC_') or die();

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('formvalidation');
$params = Component::params('com_publications');
$configs = new stdClass;
$configs->dataciteEZIDSwitch = $params->get('datacite_ezid_doi_service_switch');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (document.formvalidator.isValid($('#component-form'))) {
			Joomla.submitform(task, document.getElementById('component-form'));
		}
	}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" id="component-form" method="post" name="adminForm" autocomplete="off" class="form-validate">
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="Joomla.submitform('component.apply', this.form);"><?php echo Lang::txt('JAPPLY');?></button>
				<button type="button" onclick="Joomla.submitform('component.save', this.form);"><?php echo Lang::txt('JSAVE');?></button>
				<button type="button" onclick="<?php echo Request::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href; ' : ''; ?>window.parent.$.fancybox.close();"><?php echo Lang::txt('JCANCEL');?></button>
			</div>

			<?php echo Lang::txt($this->component->option . '_configuration'); ?>
		</div>
	</fieldset>

	<?php
	echo Html::tabs('start', 'config-tabs-' . $this->component->option . '_configuration', array('useCookie' => 1));

		if ($this->form) :
			$fieldSets = $this->form->getFieldsets();

			foreach ($fieldSets as $name => $fieldSet) :
				$label = empty($fieldSet->label) ? 'COM_CONFIG_'.$name.'_FIELDSET_LABEL' : $fieldSet->label;
				echo Html::tabs('panel', Lang::txt($label), 'publishing-details');
				if (isset($fieldSet->description) && !empty($fieldSet->description)) :
					echo '<p class="tab-description">'.Lang::txt($fieldSet->description).'</p>';
				endif;
				?>
				<ul class="config-option-list">
					<?php foreach ($this->form->getFieldset($name) as $field): ?>
						<li>
							<?php if (!$field->hidden) : ?>
								<?php echo $field->label; ?>
							<?php endif; ?>
							<?php echo $field->input; ?>
							
							<script type="text/javascript">
							<?php if ($configs->dataciteEZIDSwitch == 1) : ?>
							$("#hzform_doi_prefix-lbl").hide();
							$("#hzform_doi_prefix").hide();
							<?php endif; ?>
							
							<?php if ($configs->dataciteEZIDSwitch == 0) : ?>
							$("#hzform_doi_prefix-lbl").show();
							$("#hzform_doi_prefix").show();
							<?php endif; ?>
							
							$("#hzform_datacite_ezid_doi_service_switch").on('change', function()
							{
								if ($("#hzform_datacite_ezid_doi_service_switch option:selected").text() == 'Yes')
								{
									$("#hzform_doi_prefix-lbl").hide();
									$("#hzform_doi_prefix").hide();
								}
								else if ($("#hzform_datacite_ezid_doi_service_switch option:selected").text() == 'No')
								{
									$("#hzform_doi_prefix-lbl").show();
									$("#hzform_doi_prefix").show();
								}
							}
							);
							</script>
						</li>
					<?php endforeach; ?>
				</ul>
				<div class="clr"></div>
				<?php
			endforeach;
		else :
			echo '<p class="warning">' . Lang::txt('COM_CONFIG_ERROR_COMPONENT_CONFIG_NOT_FOUND', $this->component->option) . '</p>';
		endif;

	echo Html::tabs('end');
	?>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="path" value="<?php echo $this->model->get('component.path'); ?>" />

	<?php echo Html::input('token'); ?>
</form>
